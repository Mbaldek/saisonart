# SaisonArt

Custom WordPress child theme based on Storefront, with WooCommerce template overrides.

## Setup

1. Upload `saisonart-theme/` to `wp-content/themes/`
2. Activate **Storefront** parent theme
3. Activate **SaisonArt** child theme

## Deployment

Pushes to `main` auto-deploy via GitHub Actions (rsync over SSH).

### Required GitHub Secrets

| Secret     | Description                              |
|------------|------------------------------------------|
| `SSH_HOST` | Server hostname or IP                    |
| `SSH_USER` | SSH username                             |
| `SSH_KEY`  | Private SSH key                          |
| `SSH_PATH` | Remote path to `wp-content/themes/saisonart-theme/` |
