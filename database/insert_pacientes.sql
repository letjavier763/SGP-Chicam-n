BEGIN;

-- ============================================================================
-- 1. INSERTAR LAS 34 FAMILIAS DEL CUADERNO
-- ============================================================================
INSERT INTO familias (id_comunidad, numero_familia, apellido_cabeza, dpi, activo, fecha_registro)
VALUES
  (1, '17535', 'Maldonado Tiu', NULL, true, NOW()),
  (1, '17536', 'De León Pic', NULL, true, NOW()),
  (1, '17537', 'Max', NULL, true, NOW()),
  (1, '17538', 'Colorado Calel', NULL, true, NOW()),
  (1, '17539', 'Matta Luis', NULL, true, NOW()),
  (1, '17540', 'Yúja Chén', NULL, true, NOW()),
  (1, '17541', 'Yúja Pérez', NULL, true, NOW()),
  (1, '17542', 'Morán Morán', NULL, true, NOW()),
  (1, '17543', 'Xoy Choc', NULL, true, NOW()),
  (1, '17544', 'Chipel Chivalán', NULL, true, NOW()),
  (1, '17545', 'López Pix', NULL, true, NOW()),
  (1, '17546', 'Colorado Jom', '3415504931419', true, NOW()),
  (1, '17547', 'Tiún Benito', NULL, true, NOW()),
  (1, '17548', 'Suc Sis', NULL, true, NOW()),
  (1, '17549', 'García Pacheco', NULL, true, NOW()),
  (1, '17550', 'Chamatum Quixtán', NULL, true, NOW()),
  (1, '17551', 'Pérez Max', NULL, true, NOW()),
  (1, '17552', 'Mejía Martínez', NULL, true, NOW()),
  (1, '17553', 'Tiño Suar', NULL, true, NOW()),
  (1, '17554', 'Roca Orrego', NULL, true, NOW()),
  (1, '17555', 'Tat Choc', NULL, true, NOW()),
  (1, '17556', 'Ixkin', NULL, true, NOW()),
  (1, '17557', 'Benito', NULL, true, NOW()),
  (1, '17558', 'Mus Suc', NULL, true, NOW()),
  (1, '17559', 'González', '2337254821419', true, NOW()),
  (1, '17560', 'Morán Yúja', NULL, true, NOW()),
  (1, '17561', 'Morales Chamale', NULL, true, NOW()),
  (1, '17562', 'Yat Morán', NULL, true, NOW()),
  (1, '17563', 'Lucas Morán', NULL, true, NOW()),
  (1, '17564', 'Baylón', NULL, true, NOW()),
  (1, '17565', 'Rosales Ciprián', NULL, true, NOW()),
  (1, '17566', 'Yúja Surom', NULL, true, NOW()),
  (1, '17567', 'Alqui Choc', NULL, true, NOW()),
  (1, '17568', 'Pacay Gómez', NULL, true, NOW())
ON CONFLICT (numero_familia) DO NOTHING;

-- ============================================================================
-- 2. INSERTAR LOS 34 PACIENTES DEL CUADERNO CON CASTEOS EXPLÍCITOS
-- ============================================================================
INSERT INTO pacientes (id_family, nombres, apellidos, dpi, numero_expediente_fisico, fecha_nacimiento, sexo, parentesco_familia, activo, fecha_registro)
VALUES
  ((SELECT id_family FROM familias WHERE numero_familia = '17535'), 'Josefina', 'Maldonado Tiu', NULL, '17535', '1990-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17536'), 'Juana', 'De León Pic', NULL, '17536', '1994-08-06'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17537'), 'Raymundo', 'Max', NULL, '17537', '1985-01-01'::date, 'M', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17538'), 'Candelaria', 'Colorado Calel', NULL, '17538', '1946-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17539'), 'Glendy', 'Matta Luis', NULL, '17539', '2002-10-08'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17540'), 'Tomasa', 'Yúja Chén', NULL, '17540', '1988-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17541'), 'Sarda Mariela', 'Yúja Pérez', NULL, '17541', '2002-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17542'), 'Elvia Rosana', 'Morán Morán', NULL, '17542', '1995-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17543'), 'Sandra Floridalma', 'Xoy Choc', NULL, '17543', '1997-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17544'), 'Juan', 'Chipel Chivalán', NULL, '17544', '1989-01-01'::date, 'M', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17545'), 'Marta Fabiola', 'López Pix', NULL, '17545', '1993-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17546'), 'Lidia Marina', 'Colorado Jom', '3415504931419', '17546', '1998-05-25'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17547'), 'Bertha Adelina', 'Tiún Benito', NULL, '17547', '1996-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17548'), 'Ilida Marisol', 'Suc Sis', NULL, '17548', '2009-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17549'), 'Odilia Isabel', 'García Pacheco', NULL, '17549', '2002-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17550'), 'Marcela Bertila', 'Chamatum Quixtán', NULL, '17550', '1978-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17551'), 'Gloria Esperanza', 'Pérez Max', NULL, '17551', '2006-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17552'), 'Uraida', 'Mejía Martínez', NULL, '17552', '2006-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17553'), 'Teodora Adelaida', 'Tiño Suar', NULL, '17553', '2009-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17554'), 'Briceida Florineth', 'Roca Orrego', NULL, '17554', '2009-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17555'), 'Walter Vidal', 'Tat Choc', NULL, '17555', '2003-01-01'::date, 'M', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17556'), 'Erick Armando', 'Ixkin', NULL, '17556', '1987-01-01'::date, 'M', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17557'), 'Manuela', 'Benito', NULL, '17557', '1985-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17558'), 'Carmela', 'Mus Suc', NULL, '17558', '1974-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17559'), 'Mayra Cristal', 'González', '2337254821419', '17559', '1999-03-18'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17560'), 'Lorenza', 'Morán Yúja', NULL, '17560', '1983-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17561'), 'Gloria Isabel', 'Morales Chamale', NULL, '17561', '1982-04-02'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17562'), 'Rufina Sinta', 'Yat Morán', NULL, '17562', '2002-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17563'), 'Glenda Marisol', 'Lucas Morán', NULL, '17563', '1961-04-10'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17564'), 'Miguel Ángel', 'Baylón', NULL, '17564', '1988-01-01'::date, 'M', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17565'), 'Candelaria', 'Rosales Ciprián', NULL, '17565', '2002-08-15'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17566'), 'María Asunción', 'Yúja Surom', NULL, '17566', '1986-01-01'::date, 'F', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17567'), 'Evelio Virgilio', 'Alqui Choc', NULL, '17567', '1984-01-01'::date, 'M', 'Titular', true, NOW()),
  ((SELECT id_family FROM familias WHERE numero_familia = '17568'), 'Micaela', 'Pacay Gómez', NULL, '17568', '1991-01-01'::date, 'F', 'Titular', true, NOW());

-- ============================================================================
-- 3. SINCRONIZAR SECUENCIAS
-- ============================================================================
SELECT setval('familias_id_family_seq', COALESCE((SELECT MAX(id_family) FROM familias), 1));
SELECT setval('pacientes_id_paciente_seq', COALESCE((SELECT MAX(id_paciente) FROM pacientes), 1));

COMMIT;
