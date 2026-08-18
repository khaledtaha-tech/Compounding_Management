# Project Architecture - Compounding Management System

## Overview
A web-based system for tracking compounding production (Mixer and Pelletizer), raw materials planning, recipe management, and PDF report generation.

## File Structure & Roles

- `index.php`: Dashboard entry point displaying KPI summary cards (Production Records, Total Production, Recipes, Raw Materials, Equipment) and navigation module cards.
- `bootstrap.php`: Core application bootstrapper. Initializes session, defines `db()` PDO MySQL helper, authentication checks (`require_auth()`), and `json_response()` helper.
- `config.example.php`: Configuration template containing database credentials (`db_host`, `db_name`, `db_user`, `db_pass`).
- `config.php`: User database configuration file (created from `config.example.php`).
- `install.php`: Database setup and migration script creating tables (`users`, `equipment`, `production_records`, `material_states`).
- `login.php`: User login interface and authentication handler.
- `logout.php`: Session termination script.
- `production.php`: Main interface for Production Tracker module (Mixer and Pelletizer entry forms, daily/monthly summaries, report export controls, theme picker).
- `production-app.js`: Frontend application logic for production management, jsPDF daily/monthly report generator, theme switching, and local storage state sync.
- `production.css`: Stylesheet for the production module including CSS variables for themes (arctic, indigo, violet, midnight, graphite) and layout rules.
- `site.css`: Main styling for dashboard and global layout components.
- `materials.php`: Materials & PVC Recipes planner interface.
- `api.php`: REST API endpoint for fetching/updating production history, equipment configuration, material states, login, and backup restoration.
- `.htaccess`: Apache URL rewriting and security headers configuration.
