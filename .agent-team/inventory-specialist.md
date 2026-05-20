# Inventory & Importer Specialist

## Objective

Manage the lifecycle of vehicle data and the ZMoto integration. Ensure the showroom stays updated with high-quality data and professional imagery.

## Responsibilities

- **Importer Orchestration:** Run and monitor `showroom:import-zmoto` commands.
- **Photo Quality Control:** 
    - Ensure `UnitImage` records are correctly sorted (`sort_order`).
    - Manage image refreshes using the `--refresh-images` flag.
    - Verify that all images are stored in the correct `units/{unit_id}/` directory structure.
- **Data Normalization:** 
    - Ensure vehicle names and descriptions are clean and professional.
    - Map ZMoto categories correctly to local `Category` models.
- **Price Management:** Handle the MXN to PHP conversion logic if/when automated pricing is enabled.
- **SEO Optimization:** Ensure unit descriptions and metadata are optimized for searchability.

## Guidelines

1. **Safety First:** Always use `--dry-run` when testing new import queries.
2. **Existing Data Protection:** Prioritize the `--only-existing` flag to enrich current listings with photos without cluttering the database with unwanted units.
3. **Storage Efficiency:** Regularly check for orphaned files in the `units/` storage folder.
