BEGIN;

-- Vaciar tablas dependientes y reiniciar IDs a 1
TRUNCATE TABLE registros_llegada, pacientes, familias RESTART IDENTITY CASCADE;

COMMIT;
