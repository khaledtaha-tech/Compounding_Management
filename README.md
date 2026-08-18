# Compounding Management System

Unified PHP/MySQL application for Hostinger.

## Modules
- Mixer and Pelletizer Production Tracker
- PVC Recipes and Raw Materials Planner
- Stock Coverage
- Daily and Monthly PDF reports
- Complete Excel backup and restore
- Production Code (MP/MF) linked to Recipe Code (KH)
- Mixer entry uses the saved recipe list and fills code, name, and color automatically
- One-page A4 portrait daily report with separate Mixer and Pelletizer sections, visual product colors, weekday, KPI totals, and shift totals
- Raw-material Excel imports match by both Material Name and Grade / Trade Name
- Local email/password login (no Firebase, Supabase, or Render)

## Install
1. Copy `config.example.php` to `config.php` and enter the Hostinger MySQL password.
2. Upload the project files to `public_html`.
3. Open `/install.php` once and create the first account.
4. Sign in, then import the Material Planner full backup. Import a Production backup only when you are restoring production history on a new database.

## Upgrade from V2
1. Replace the project files, but keep the existing `config.php` on Hostinger.
2. Open `/install.php` once. It will add the new `recipe_code` database column without deleting records.
3. Open Materials & Recipes and use **Import Full Backup** with the cleaned V3 material backup.
4. Open Production and confirm that the Mix / Recipe list shows MP/MF and KH codes before saving a new record.

Existing production records, users, equipment, and database data remain in place.
