<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$user = current_user();
if (!$user) json_response(['error' => 'Your session has expired. Please sign in again.'], 401);
$pdo = db();
$action = (string) ($_GET['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];

function clean(string $value, int $max = 190): string
{
    $value = trim($value);
    return function_exists('mb_substr') ? mb_substr($value, 0, $max) : substr($value, 0, $max);
}

function iso_now(): string
{
    return gmdate('Y-m-d H:i:s');
}

try {
    if ($action === 'session' && $method === 'GET') {
        json_response(['user' => $user]);
    }

    if ($action === 'equipment') {
        if ($method === 'GET') {
            $stmt = $pdo->prepare('SELECT id, type, name FROM equipment WHERE user_id = ? ORDER BY type, name');
            $stmt->execute([$user['id']]);
            $out = ['mixers' => [], 'pelletizers' => []];
            foreach ($stmt as $row) {
                $item = ['id' => $row['id'], 'name' => $row['name']];
                $out[$row['type'] === 'Mixer' ? 'mixers' : 'pelletizers'][] = $item;
            }
            json_response($out);
        }
        if ($method === 'POST') {
            $type = clean((string) ($body['type'] ?? ''), 20);
            $name = clean((string) ($body['name'] ?? ''), 100);
            if (!in_array($type, ['Mixer', 'Pelletizer'], true)) json_response(['error' => 'Invalid equipment type.'], 422);
            if ($name === '') json_response(['error' => 'Equipment name is required.'], 422);
            $id = clean((string) ($body['id'] ?? ''), 80);
            if ($id === '') $id = strtolower($type) . '-' . bin2hex(random_bytes(8));
            $stmt = $pdo->prepare('INSERT INTO equipment (id,user_id,type,name) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name)');
            $stmt->execute([$id, $user['id'], $type, $name]);
            json_response(['id' => $id, 'name' => $name, 'type' => $type], 201);
        }
        if ($method === 'DELETE') {
            $id = clean((string) ($_GET['id'] ?? ''), 80);
            $stmt = $pdo->prepare('DELETE FROM equipment WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $user['id']]);
            json_response(['ok' => true]);
        }
    }

    if ($action === 'records') {
        if ($method === 'GET') {
            $stmt = $pdo->prepare('SELECT * FROM production_records WHERE user_id = ? ORDER BY production_date DESC, created_at DESC');
            $stmt->execute([$user['id']]);
            $records = [];
            foreach ($stmt as $row) {
                $isMixer = $row['type'] === 'Mixer';
                $records[] = [
                    'id' => $row['id'], 'type' => $row['type'], 'date' => $row['production_date'],
                    'shift' => $row['shift'], 'color' => $row['color'], 'quantityKg' => (float) $row['quantity_kg'],
                    'mixerId' => $isMixer ? ($row['equipment_id'] ?? '') : '',
                    'mixerName' => $isMixer ? $row['equipment_name'] : '',
                    'mixCode' => $isMixer ? ($row['mix_code'] ?? '') : '',
                    'mixName' => $isMixer ? ($row['mix_name'] ?? '') : '',
                    'pelletizerId' => !$isMixer ? ($row['equipment_id'] ?? '') : '',
                    'pelletizerName' => !$isMixer ? $row['equipment_name'] : '',
                    'application' => !$isMixer ? ($row['pellet_application'] ?? '') : '',
                    'createdAt' => str_replace(' ', 'T', $row['created_at']) . 'Z',
                    'updatedAt' => str_replace(' ', 'T', $row['updated_at']) . 'Z',
                ];
            }
            json_response($records);
        }

        if ($method === 'POST' || $method === 'PUT') {
            $type = clean((string) ($body['type'] ?? ''), 20);
            $date = clean((string) ($body['date'] ?? ''), 10);
            $shift = clean((string) ($body['shift'] ?? ''), 20);
            $color = clean((string) ($body['color'] ?? ''), 100);
            $quantity = round((float) ($body['quantityKg'] ?? 0), 2);
            if (!in_array($type, ['Mixer', 'Pelletizer'], true)) json_response(['error' => 'Choose Mixer or Pelletizer.'], 422);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) json_response(['error' => 'A valid date is required.'], 422);
            if ($shift === 'Evening') $shift = 'Night';
            if (!in_array($shift, ['Morning', 'Night'], true)) json_response(['error' => 'Choose Morning or Night shift.'], 422);
            if ($color === '' || $quantity <= 0) json_response(['error' => 'Color and a positive quantity are required.'], 422);

            $equipmentId = clean((string) ($type === 'Mixer' ? ($body['mixerId'] ?? '') : ($body['pelletizerId'] ?? '')), 80);
            $equipmentName = clean((string) ($type === 'Mixer' ? ($body['mixerName'] ?? '') : ($body['pelletizerName'] ?? '')), 100);
            if ($equipmentName === '') json_response(['error' => 'Equipment is required.'], 422);
            $mixCode = $type === 'Mixer' ? clean((string) ($body['mixCode'] ?? ''), 80) : '';
            $mixName = $type === 'Mixer' ? clean((string) ($body['mixName'] ?? ''), 190) : '';
            $application = $type === 'Pelletizer' ? clean((string) ($body['application'] ?? ''), 190) : '';
            if ($type === 'Mixer' && ($mixCode === '' || $mixName === '')) json_response(['error' => 'Mix code and mix name are required.'], 422);
            if ($type === 'Pelletizer' && $application === '') json_response(['error' => 'Pellet application is required.'], 422);

            $id = clean((string) ($_GET['id'] ?? $body['id'] ?? ''), 80);
            $created = iso_now();
            if ($method === 'POST') {
                if ($id === '') $id = bin2hex(random_bytes(10));
                $stmt = $pdo->prepare('INSERT INTO production_records (id,user_id,type,production_date,shift,equipment_id,equipment_name,mix_code,mix_name,pellet_application,color,quantity_kg,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE type=VALUES(type),production_date=VALUES(production_date),shift=VALUES(shift),equipment_id=VALUES(equipment_id),equipment_name=VALUES(equipment_name),mix_code=VALUES(mix_code),mix_name=VALUES(mix_name),pellet_application=VALUES(pellet_application),color=VALUES(color),quantity_kg=VALUES(quantity_kg),updated_at=VALUES(updated_at)');
                $stmt->execute([$id,$user['id'],$type,$date,$shift,$equipmentId ?: null,$equipmentName,$mixCode ?: null,$mixName ?: null,$application ?: null,$color,$quantity,$created,$created]);
            } else {
                if ($id === '') json_response(['error' => 'Record ID is required.'], 422);
                $stmt = $pdo->prepare('UPDATE production_records SET type=?,production_date=?,shift=?,equipment_id=?,equipment_name=?,mix_code=?,mix_name=?,pellet_application=?,color=?,quantity_kg=?,updated_at=? WHERE id=? AND user_id=?');
                $stmt->execute([$type,$date,$shift,$equipmentId ?: null,$equipmentName,$mixCode ?: null,$mixName ?: null,$application ?: null,$color,$quantity,$created,$id,$user['id']]);
            }
            json_response(['id' => $id, 'ok' => true], $method === 'POST' ? 201 : 200);
        }
        if ($method === 'DELETE') {
            $id = clean((string) ($_GET['id'] ?? ''), 80);
            $stmt = $pdo->prepare('DELETE FROM production_records WHERE id=? AND user_id=?');
            $stmt->execute([$id,$user['id']]);
            json_response(['ok' => true]);
        }
    }

    if ($action === 'material_state') {
        if ($method === 'GET') {
            $stmt = $pdo->prepare('SELECT state_json, updated_at FROM material_states WHERE user_id=?');
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch();
            json_response(['state' => $row ? json_decode($row['state_json'], true) : null, 'updatedAt' => $row['updated_at'] ?? null]);
        }
        if ($method === 'PUT' || $method === 'POST') {
            $state = $body['state'] ?? null;
            if (!is_array($state) || !isset($state['recipes'])) json_response(['error' => 'Invalid material planner data.'], 422);
            $json = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false || strlen($json) > 8_000_000) json_response(['error' => 'Material data is too large.'], 413);
            $stmt = $pdo->prepare('INSERT INTO material_states (user_id,state_json,updated_at) VALUES (?,?,?) ON DUPLICATE KEY UPDATE state_json=VALUES(state_json),updated_at=VALUES(updated_at)');
            $stmt->execute([$user['id'],$json,iso_now()]);
            json_response(['ok' => true]);
        }
    }

    json_response(['error' => 'Unknown API action.'], 404);
} catch (PDOException $e) {
    $message = $e->getCode() === '23000' ? 'This item already exists.' : 'Database operation failed.';
    json_response(['error' => $message], 500);
} catch (Throwable $e) {
    json_response(['error' => $e->getMessage()], 500);
}
