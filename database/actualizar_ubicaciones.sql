BEGIN;

-- ============================================================================
-- 1. INSERTAR COMUNIDADES / ALDEAS DEL CUADERNO EN EL CATÁLOGO
-- ============================================================================
INSERT INTO comunidades (id_comunidad, id_municipio, nombre, zona)
VALUES
  (10, 1401, 'Agua Zarca', 'Rural'),
  (11, 1401, 'Aguazarca', 'Rural'),
  (12, 1401, 'Amax', 'Rural'),
  (13, 1401, 'Beleju', 'Rural'),
  (14, 1401, 'Belejú', 'Rural'),
  (15, 1401, 'Belgica', 'Rural'),
  (16, 1401, 'Buena Vista', 'Rural'),
  (17, 1401, 'Buena Vista el Amay', 'Rural'),
  (18, 1401, 'Capilla Chiyuta Belejú', 'Rural'),
  (19, 1401, 'Carrizal', 'Rural'),
  (20, 1401, 'Cebollin', 'Rural'),
  (21, 1401, 'Chelas', 'Rural'),
  (22, 1401, 'Chexiquin', 'Rural'),
  (23, 1401, 'Chicaman', 'Rural'),
  (24, 1401, 'Chicamán', 'Rural'),
  (25, 1401, 'Chinajedn', 'Rural'),
  (26, 1401, 'Chipoj', 'Rural'),
  (27, 1401, 'Chixoy', 'Rural'),
  (28, 1401, 'Choconal', 'Rural'),
  (29, 1401, 'Chocoral', 'Rural'),
  (30, 1401, 'Chocorral', 'Rural'),
  (31, 1401, 'Chojoj', 'Rural'),
  (32, 1401, 'Chupoj', 'Rural'),
  (33, 1401, 'Clinica Los Calel', 'Rural'),
  (34, 1401, 'Coban, A.V.', 'Rural'),
  (35, 1401, 'Concepcion', 'Rural'),
  (36, 1401, 'Conche', 'Rural'),
  (37, 1401, 'Cruz Chut', 'Rural'),
  (38, 1401, 'Cumbre el Amay', 'Rural'),
  (39, 1401, 'El Amay', 'Rural'),
  (40, 1401, 'El Palmar', 'Rural'),
  (41, 1401, 'El Pinal', 'Rural'),
  (42, 1401, 'El Pincel', 'Rural'),
  (43, 1401, 'El Salin', 'Rural'),
  (44, 1401, 'El Soch', 'Rural'),
  (45, 1401, 'Enruchut', 'Rural'),
  (46, 1401, 'Espolon', 'Rural'),
  (47, 1401, 'Esquipulas', 'Rural'),
  (48, 1401, 'Esquipulas Pajvil', 'Rural'),
  (49, 1401, 'Estajul', 'Rural'),
  (50, 1401, 'Estayul', 'Rural'),
  (51, 1401, 'Hacienda Belejú', 'Rural'),
  (52, 1401, 'Joyabaj', 'Rural'),
  (53, 1401, 'Jumuc', 'Rural'),
  (54, 1401, 'La Campana', 'Rural'),
  (55, 1401, 'La Cantera', 'Rural'),
  (56, 1401, 'La Garzona', 'Rural'),
  (57, 1401, 'La Lagunita', 'Rural'),
  (58, 1401, 'La Lima', 'Rural'),
  (59, 1401, 'La Navidad San Cristobal A.V.', 'Rural'),
  (60, 1401, 'La Pita Friga (A.V.)', 'Rural'),
  (61, 1401, 'La Soledad', 'Rural'),
  (62, 1401, 'Laruz', 'Rural'),
  (63, 1401, 'Las Pacayas', 'Rural'),
  (64, 1401, 'Llano Grande', 'Rural'),
  (65, 1401, 'Lopez Amax', 'Rural'),
  (66, 1401, 'Lopez Amay', 'Rural'),
  (67, 1401, 'Los Chinos', 'Rural'),
  (68, 1401, 'Los Pacayas', 'Rural'),
  (69, 1401, 'Los Planes', 'Rural'),
  (70, 1401, 'Los Platanos', 'Rural'),
  (71, 1401, 'Los Pósitos', 'Rural'),
  (72, 1401, 'Lote 11', 'Rural'),
  (73, 1401, 'Monte Maria', 'Rural'),
  (74, 1401, 'Nueva Esperanza', 'Rural'),
  (75, 1401, 'Ojo de Agua', 'Rural'),
  (76, 1401, 'Pajales', 'Rural'),
  (77, 1401, 'Pajales Cahabon', 'Rural'),
  (78, 1401, 'Pajecul', 'Rural'),
  (79, 1401, 'Pajul', 'Rural'),
  (80, 1401, 'Pajvil', 'Rural'),
  (81, 1401, 'Pancur', 'Rural'),
  (82, 1401, 'Pinal', 'Rural'),
  (83, 1401, 'Portal', 'Rural'),
  (84, 1401, 'Pujiul', 'Rural'),
  (85, 1401, 'Raxruha', 'Rural'),
  (86, 1401, 'S.A. la ESP.', 'Rural'),
  (87, 1401, 'Sacac', 'Rural'),
  (88, 1401, 'Sach', 'Rural'),
  (89, 1401, 'Sahtoc Beleju', 'Rural'),
  (90, 1401, 'Sahtoc El Pinal', 'Rural'),
  (91, 1401, 'San Antonio Beleju', 'Rural'),
  (92, 1401, 'San Antonio la Esperanza', 'Rural'),
  (93, 1401, 'San Pedro Belejú', 'Rural'),
  (94, 1401, 'San Sebastian', 'Rural'),
  (95, 1401, 'San Sebastian Belejú', 'Rural'),
  (96, 1401, 'San Vicente Pacaya', 'Rural'),
  (97, 1401, 'Santa Ana Zona 2', 'Rural'),
  (98, 1401, 'Santa Rosa Yalagüitz', 'Rural'),
  (99, 1401, 'Sobre Soca', 'Rural'),
  (100, 1401, 'Tríbol', 'Rural'),
  (101, 1401, 'Uspantan', 'Rural'),
  (102, 1401, 'Uspantán', 'Rural'),
  (103, 1401, 'Vrecor Ruiz Pinal', 'Rural'),
  (104, 1401, 'Xecaguic', 'Rural'),
  (105, 1401, 'Xecaquic', 'Rural'),
  (106, 1401, 'Xecaquic - Beleju', 'Rural'),
  (107, 1401, 'Zona Retirad.', 'Rural'),
  (108, 1401, 'puente seco', 'Rural')
ON CONFLICT (id_comunidad) DO UPDATE SET nombre = EXCLUDED.nombre;

-- ============================================================================
-- 2. ACTUALIZAR ID_COMUNIDAD EN LAS FAMILIAS CORRESPONDIENTES
-- ============================================================================
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17402';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17403';
UPDATE familias SET id_comunidad = 44 WHERE numero_familia = '17404';
UPDATE familias SET id_comunidad = 27 WHERE numero_familia = '17405';
UPDATE familias SET id_comunidad = 29 WHERE numero_familia = '17406';
UPDATE familias SET id_comunidad = 97 WHERE numero_familia = '17407';
UPDATE familias SET id_comunidad = 54 WHERE numero_familia = '17408';
UPDATE familias SET id_comunidad = 73 WHERE numero_familia = '17409';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17410';
UPDATE familias SET id_comunidad = 32 WHERE numero_familia = '17411';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17412';
UPDATE familias SET id_comunidad = 76 WHERE numero_familia = '17413';
UPDATE familias SET id_comunidad = 104 WHERE numero_familia = '17414';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17415';
UPDATE familias SET id_comunidad = 101 WHERE numero_familia = '17416';
UPDATE familias SET id_comunidad = 60 WHERE numero_familia = '17418';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17419';
UPDATE familias SET id_comunidad = 68 WHERE numero_familia = '17420';
UPDATE familias SET id_comunidad = 64 WHERE numero_familia = '17421';
UPDATE familias SET id_comunidad = 86 WHERE numero_familia = '17422';
UPDATE familias SET id_comunidad = 39 WHERE numero_familia = '17423';
UPDATE familias SET id_comunidad = 74 WHERE numero_familia = '17424';
UPDATE familias SET id_comunidad = 29 WHERE numero_familia = '17425';
UPDATE familias SET id_comunidad = 29 WHERE numero_familia = '17426';
UPDATE familias SET id_comunidad = 72 WHERE numero_familia = '17427';
UPDATE familias SET id_comunidad = 80 WHERE numero_familia = '17428';
UPDATE familias SET id_comunidad = 108 WHERE numero_familia = '17429';
UPDATE familias SET id_comunidad = 15 WHERE numero_familia = '17430';
UPDATE familias SET id_comunidad = 107 WHERE numero_familia = '17431';
UPDATE familias SET id_comunidad = 27 WHERE numero_familia = '17432';
UPDATE familias SET id_comunidad = 29 WHERE numero_familia = '17433';
UPDATE familias SET id_comunidad = 76 WHERE numero_familia = '17434';
UPDATE familias SET id_comunidad = 61 WHERE numero_familia = '17435';
UPDATE familias SET id_comunidad = 57 WHERE numero_familia = '17436';
UPDATE familias SET id_comunidad = 59 WHERE numero_familia = '17437';
UPDATE familias SET id_comunidad = 48 WHERE numero_familia = '17438';
UPDATE familias SET id_comunidad = 76 WHERE numero_familia = '17439';
UPDATE familias SET id_comunidad = 54 WHERE numero_familia = '17440';
UPDATE familias SET id_comunidad = 74 WHERE numero_familia = '17441';
UPDATE familias SET id_comunidad = 76 WHERE numero_familia = '17442';
UPDATE familias SET id_comunidad = 95 WHERE numero_familia = '17443';
UPDATE familias SET id_comunidad = 77 WHERE numero_familia = '17444';
UPDATE familias SET id_comunidad = 58 WHERE numero_familia = '17445';
UPDATE familias SET id_comunidad = 24 WHERE numero_familia = '17446';
UPDATE familias SET id_comunidad = 71 WHERE numero_familia = '17447';
UPDATE familias SET id_comunidad = 14 WHERE numero_familia = '17448';
UPDATE familias SET id_comunidad = 47 WHERE numero_familia = '17449';
UPDATE familias SET id_comunidad = 42 WHERE numero_familia = '17450';
UPDATE familias SET id_comunidad = 38 WHERE numero_familia = '17451';
UPDATE familias SET id_comunidad = 102 WHERE numero_familia = '17452';
UPDATE familias SET id_comunidad = 87 WHERE numero_familia = '17453';
UPDATE familias SET id_comunidad = 56 WHERE numero_familia = '17454';
UPDATE familias SET id_comunidad = 18 WHERE numero_familia = '17455';
UPDATE familias SET id_comunidad = 51 WHERE numero_familia = '17456';
UPDATE familias SET id_comunidad = 81 WHERE numero_familia = '17457';
UPDATE familias SET id_comunidad = 39 WHERE numero_familia = '17458';
UPDATE familias SET id_comunidad = 27 WHERE numero_familia = '17459';
UPDATE familias SET id_comunidad = 14 WHERE numero_familia = '17460';
UPDATE familias SET id_comunidad = 25 WHERE numero_familia = '17461';
UPDATE familias SET id_comunidad = 79 WHERE numero_familia = '17462';
UPDATE familias SET id_comunidad = 50 WHERE numero_familia = '17463';
UPDATE familias SET id_comunidad = 26 WHERE numero_familia = '17464';
UPDATE familias SET id_comunidad = 14 WHERE numero_familia = '17465';
UPDATE familias SET id_comunidad = 98 WHERE numero_familia = '17466';
UPDATE familias SET id_comunidad = 85 WHERE numero_familia = '17467';
UPDATE familias SET id_comunidad = 10 WHERE numero_familia = '17468';
UPDATE familias SET id_comunidad = 28 WHERE numero_familia = '17469';
UPDATE familias SET id_comunidad = 75 WHERE numero_familia = '17471';
UPDATE familias SET id_comunidad = 93 WHERE numero_familia = '17472';
UPDATE familias SET id_comunidad = 37 WHERE numero_familia = '17473';
UPDATE familias SET id_comunidad = 79 WHERE numero_familia = '17474';
UPDATE familias SET id_comunidad = 27 WHERE numero_familia = '17475';
UPDATE familias SET id_comunidad = 27 WHERE numero_familia = '17476';
UPDATE familias SET id_comunidad = 66 WHERE numero_familia = '17477';
UPDATE familias SET id_comunidad = 11 WHERE numero_familia = '17478';
UPDATE familias SET id_comunidad = 67 WHERE numero_familia = '17479';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17480';
UPDATE familias SET id_comunidad = 101 WHERE numero_familia = '17481';
UPDATE familias SET id_comunidad = 100 WHERE numero_familia = '17482';
UPDATE familias SET id_comunidad = 78 WHERE numero_familia = '17488';
UPDATE familias SET id_comunidad = 41 WHERE numero_familia = '17489';
UPDATE familias SET id_comunidad = 103 WHERE numero_familia = '17490';
UPDATE familias SET id_comunidad = 55 WHERE numero_familia = '17491';
UPDATE familias SET id_comunidad = 12 WHERE numero_familia = '17492';
UPDATE familias SET id_comunidad = 35 WHERE numero_familia = '17493';
UPDATE familias SET id_comunidad = 92 WHERE numero_familia = '17494';
UPDATE familias SET id_comunidad = 31 WHERE numero_familia = '17495';
UPDATE familias SET id_comunidad = 12 WHERE numero_familia = '17496';
UPDATE familias SET id_comunidad = 63 WHERE numero_familia = '17497';
UPDATE familias SET id_comunidad = 13 WHERE numero_familia = '17498';
UPDATE familias SET id_comunidad = 13 WHERE numero_familia = '17499';
UPDATE familias SET id_comunidad = 54 WHERE numero_familia = '17500';
UPDATE familias SET id_comunidad = 70 WHERE numero_familia = '17501';
UPDATE familias SET id_comunidad = 101 WHERE numero_familia = '17502';
UPDATE familias SET id_comunidad = 49 WHERE numero_familia = '17504';
UPDATE familias SET id_comunidad = 78 WHERE numero_familia = '17505';
UPDATE familias SET id_comunidad = 70 WHERE numero_familia = '17506';
UPDATE familias SET id_comunidad = 13 WHERE numero_familia = '17507';
UPDATE familias SET id_comunidad = 16 WHERE numero_familia = '17508';
UPDATE familias SET id_comunidad = 65 WHERE numero_familia = '17509';
UPDATE familias SET id_comunidad = 45 WHERE numero_familia = '17510';
UPDATE familias SET id_comunidad = 88 WHERE numero_familia = '17511';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17522';
UPDATE familias SET id_comunidad = 64 WHERE numero_familia = '17524';
UPDATE familias SET id_comunidad = 89 WHERE numero_familia = '17525';
UPDATE familias SET id_comunidad = 101 WHERE numero_familia = '17526';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17527';
UPDATE familias SET id_comunidad = 82 WHERE numero_familia = '17528';
UPDATE familias SET id_comunidad = 90 WHERE numero_familia = '17529';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17530';
UPDATE familias SET id_comunidad = 46 WHERE numero_familia = '17531';
UPDATE familias SET id_comunidad = 36 WHERE numero_familia = '17532';
UPDATE familias SET id_comunidad = 96 WHERE numero_familia = '17533';
UPDATE familias SET id_comunidad = 43 WHERE numero_familia = '17534';
UPDATE familias SET id_comunidad = 69 WHERE numero_familia = '17535';
UPDATE familias SET id_comunidad = 30 WHERE numero_familia = '17536';
UPDATE familias SET id_comunidad = 81 WHERE numero_familia = '17537';
UPDATE familias SET id_comunidad = 84 WHERE numero_familia = '17538';
UPDATE familias SET id_comunidad = 22 WHERE numero_familia = '17539';
UPDATE familias SET id_comunidad = 17 WHERE numero_familia = '17540';
UPDATE familias SET id_comunidad = 21 WHERE numero_familia = '17541';
UPDATE familias SET id_comunidad = 54 WHERE numero_familia = '17542';
UPDATE familias SET id_comunidad = 91 WHERE numero_familia = '17543';
UPDATE familias SET id_comunidad = 19 WHERE numero_familia = '17544';
UPDATE familias SET id_comunidad = 106 WHERE numero_familia = '17545';
UPDATE familias SET id_comunidad = 94 WHERE numero_familia = '17546';
UPDATE familias SET id_comunidad = 105 WHERE numero_familia = '17547';
UPDATE familias SET id_comunidad = 53 WHERE numero_familia = '17548';
UPDATE familias SET id_comunidad = 83 WHERE numero_familia = '17550';
UPDATE familias SET id_comunidad = 27 WHERE numero_familia = '17551';
UPDATE familias SET id_comunidad = 62 WHERE numero_familia = '17552';
UPDATE familias SET id_comunidad = 52 WHERE numero_familia = '17553';
UPDATE familias SET id_comunidad = 13 WHERE numero_familia = '17556';
UPDATE familias SET id_comunidad = 10 WHERE numero_familia = '17557';
UPDATE familias SET id_comunidad = 13 WHERE numero_familia = '17558';
UPDATE familias SET id_comunidad = 20 WHERE numero_familia = '17559';
UPDATE familias SET id_comunidad = 53 WHERE numero_familia = '17560';
UPDATE familias SET id_comunidad = 40 WHERE numero_familia = '17561';
UPDATE familias SET id_comunidad = 41 WHERE numero_familia = '17562';
UPDATE familias SET id_comunidad = 13 WHERE numero_familia = '17563';
UPDATE familias SET id_comunidad = 23 WHERE numero_familia = '17564';
UPDATE familias SET id_comunidad = 33 WHERE numero_familia = '17565';
UPDATE familias SET id_comunidad = 99 WHERE numero_familia = '17566';
UPDATE familias SET id_comunidad = 34 WHERE numero_familia = '17567';
UPDATE familias SET id_comunidad = 39 WHERE numero_familia = '17568';

-- ============================================================================
-- 3. ACTUALIZAR DIRECCIÓN EN LOS PACIENTES
-- ============================================================================
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17402';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17403';
UPDATE pacientes SET direccion = 'El Soch' WHERE numero_expediente_fisico = '17404';
UPDATE pacientes SET direccion = 'Chixoy' WHERE numero_expediente_fisico = '17405';
UPDATE pacientes SET direccion = 'Chocoral' WHERE numero_expediente_fisico = '17406';
UPDATE pacientes SET direccion = 'Santa Ana Zona 2' WHERE numero_expediente_fisico = '17407';
UPDATE pacientes SET direccion = 'La Campana' WHERE numero_expediente_fisico = '17408';
UPDATE pacientes SET direccion = 'Monte Maria' WHERE numero_expediente_fisico = '17409';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17410';
UPDATE pacientes SET direccion = 'Chupoj' WHERE numero_expediente_fisico = '17411';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17412';
UPDATE pacientes SET direccion = 'Pajales' WHERE numero_expediente_fisico = '17413';
UPDATE pacientes SET direccion = 'Xecaguic' WHERE numero_expediente_fisico = '17414';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17415';
UPDATE pacientes SET direccion = 'Uspantan' WHERE numero_expediente_fisico = '17416';
UPDATE pacientes SET direccion = 'La Pita Friga (A.V.)' WHERE numero_expediente_fisico = '17418';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17419';
UPDATE pacientes SET direccion = 'Los Pacayas' WHERE numero_expediente_fisico = '17420';
UPDATE pacientes SET direccion = 'Llano Grande' WHERE numero_expediente_fisico = '17421';
UPDATE pacientes SET direccion = 'S.A. la ESP.' WHERE numero_expediente_fisico = '17422';
UPDATE pacientes SET direccion = 'El Amay' WHERE numero_expediente_fisico = '17423';
UPDATE pacientes SET direccion = 'Nueva Esperanza' WHERE numero_expediente_fisico = '17424';
UPDATE pacientes SET direccion = 'Chocoral' WHERE numero_expediente_fisico = '17425';
UPDATE pacientes SET direccion = 'Chocoral' WHERE numero_expediente_fisico = '17426';
UPDATE pacientes SET direccion = 'Lote 11' WHERE numero_expediente_fisico = '17427';
UPDATE pacientes SET direccion = 'Pajvil' WHERE numero_expediente_fisico = '17428';
UPDATE pacientes SET direccion = 'puente seco' WHERE numero_expediente_fisico = '17429';
UPDATE pacientes SET direccion = 'Belgica' WHERE numero_expediente_fisico = '17430';
UPDATE pacientes SET direccion = 'Zona Retirad.' WHERE numero_expediente_fisico = '17431';
UPDATE pacientes SET direccion = 'Chixoy' WHERE numero_expediente_fisico = '17432';
UPDATE pacientes SET direccion = 'Chocoral' WHERE numero_expediente_fisico = '17433';
UPDATE pacientes SET direccion = 'Pajales' WHERE numero_expediente_fisico = '17434';
UPDATE pacientes SET direccion = 'La Soledad' WHERE numero_expediente_fisico = '17435';
UPDATE pacientes SET direccion = 'La Lagunita' WHERE numero_expediente_fisico = '17436';
UPDATE pacientes SET direccion = 'La Navidad San Cristobal A.V.' WHERE numero_expediente_fisico = '17437';
UPDATE pacientes SET direccion = 'Esquipulas Pajvil' WHERE numero_expediente_fisico = '17438';
UPDATE pacientes SET direccion = 'Pajales' WHERE numero_expediente_fisico = '17439';
UPDATE pacientes SET direccion = 'La Campana' WHERE numero_expediente_fisico = '17440';
UPDATE pacientes SET direccion = 'Nueva Esperanza' WHERE numero_expediente_fisico = '17441';
UPDATE pacientes SET direccion = 'Pajales' WHERE numero_expediente_fisico = '17442';
UPDATE pacientes SET direccion = 'San Sebastian Belejú' WHERE numero_expediente_fisico = '17443';
UPDATE pacientes SET direccion = 'Pajales Cahabon' WHERE numero_expediente_fisico = '17444';
UPDATE pacientes SET direccion = 'La Lima' WHERE numero_expediente_fisico = '17445';
UPDATE pacientes SET direccion = 'Chicamán' WHERE numero_expediente_fisico = '17446';
UPDATE pacientes SET direccion = 'Los Pósitos' WHERE numero_expediente_fisico = '17447';
UPDATE pacientes SET direccion = 'Belejú' WHERE numero_expediente_fisico = '17448';
UPDATE pacientes SET direccion = 'Esquipulas' WHERE numero_expediente_fisico = '17449';
UPDATE pacientes SET direccion = 'El Pincel' WHERE numero_expediente_fisico = '17450';
UPDATE pacientes SET direccion = 'Cumbre el Amay' WHERE numero_expediente_fisico = '17451';
UPDATE pacientes SET direccion = 'Uspantán' WHERE numero_expediente_fisico = '17452';
UPDATE pacientes SET direccion = 'Sacac' WHERE numero_expediente_fisico = '17453';
UPDATE pacientes SET direccion = 'La Garzona' WHERE numero_expediente_fisico = '17454';
UPDATE pacientes SET direccion = 'Capilla Chiyuta Belejú' WHERE numero_expediente_fisico = '17455';
UPDATE pacientes SET direccion = 'Hacienda Belejú' WHERE numero_expediente_fisico = '17456';
UPDATE pacientes SET direccion = 'Pancur' WHERE numero_expediente_fisico = '17457';
UPDATE pacientes SET direccion = 'El Amay' WHERE numero_expediente_fisico = '17458';
UPDATE pacientes SET direccion = 'Chixoy' WHERE numero_expediente_fisico = '17459';
UPDATE pacientes SET direccion = 'Belejú' WHERE numero_expediente_fisico = '17460';
UPDATE pacientes SET direccion = 'Chinajedn' WHERE numero_expediente_fisico = '17461';
UPDATE pacientes SET direccion = 'Pajul' WHERE numero_expediente_fisico = '17462';
UPDATE pacientes SET direccion = 'Estayul' WHERE numero_expediente_fisico = '17463';
UPDATE pacientes SET direccion = 'Chipoj' WHERE numero_expediente_fisico = '17464';
UPDATE pacientes SET direccion = 'Belejú' WHERE numero_expediente_fisico = '17465';
UPDATE pacientes SET direccion = 'Santa Rosa Yalagüitz' WHERE numero_expediente_fisico = '17466';
UPDATE pacientes SET direccion = 'Raxruha' WHERE numero_expediente_fisico = '17467';
UPDATE pacientes SET direccion = 'Agua Zarca' WHERE numero_expediente_fisico = '17468';
UPDATE pacientes SET direccion = 'Choconal' WHERE numero_expediente_fisico = '17469';
UPDATE pacientes SET direccion = 'Ojo de Agua' WHERE numero_expediente_fisico = '17471';
UPDATE pacientes SET direccion = 'San Pedro Belejú' WHERE numero_expediente_fisico = '17472';
UPDATE pacientes SET direccion = 'Cruz Chut' WHERE numero_expediente_fisico = '17473';
UPDATE pacientes SET direccion = 'Pajul' WHERE numero_expediente_fisico = '17474';
UPDATE pacientes SET direccion = 'Chixoy' WHERE numero_expediente_fisico = '17475';
UPDATE pacientes SET direccion = 'Chixoy' WHERE numero_expediente_fisico = '17476';
UPDATE pacientes SET direccion = 'Lopez Amay' WHERE numero_expediente_fisico = '17477';
UPDATE pacientes SET direccion = 'Aguazarca' WHERE numero_expediente_fisico = '17478';
UPDATE pacientes SET direccion = 'Los Chinos' WHERE numero_expediente_fisico = '17479';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17480';
UPDATE pacientes SET direccion = 'Uspantan' WHERE numero_expediente_fisico = '17481';
UPDATE pacientes SET direccion = 'Tríbol' WHERE numero_expediente_fisico = '17482';
UPDATE pacientes SET direccion = 'Pajecul' WHERE numero_expediente_fisico = '17488';
UPDATE pacientes SET direccion = 'El Pinal' WHERE numero_expediente_fisico = '17489';
UPDATE pacientes SET direccion = 'Vrecor Ruiz Pinal' WHERE numero_expediente_fisico = '17490';
UPDATE pacientes SET direccion = 'La Cantera' WHERE numero_expediente_fisico = '17491';
UPDATE pacientes SET direccion = 'Amax' WHERE numero_expediente_fisico = '17492';
UPDATE pacientes SET direccion = 'Concepcion' WHERE numero_expediente_fisico = '17493';
UPDATE pacientes SET direccion = 'San Antonio la Esperanza' WHERE numero_expediente_fisico = '17494';
UPDATE pacientes SET direccion = 'Chojoj' WHERE numero_expediente_fisico = '17495';
UPDATE pacientes SET direccion = 'Amax' WHERE numero_expediente_fisico = '17496';
UPDATE pacientes SET direccion = 'Las Pacayas' WHERE numero_expediente_fisico = '17497';
UPDATE pacientes SET direccion = 'Beleju' WHERE numero_expediente_fisico = '17498';
UPDATE pacientes SET direccion = 'Beleju' WHERE numero_expediente_fisico = '17499';
UPDATE pacientes SET direccion = 'La Campana' WHERE numero_expediente_fisico = '17500';
UPDATE pacientes SET direccion = 'Los Platanos' WHERE numero_expediente_fisico = '17501';
UPDATE pacientes SET direccion = 'Uspantan' WHERE numero_expediente_fisico = '17502';
UPDATE pacientes SET direccion = 'Estajul' WHERE numero_expediente_fisico = '17504';
UPDATE pacientes SET direccion = 'Pajecul' WHERE numero_expediente_fisico = '17505';
UPDATE pacientes SET direccion = 'Los Platanos' WHERE numero_expediente_fisico = '17506';
UPDATE pacientes SET direccion = 'Beleju' WHERE numero_expediente_fisico = '17507';
UPDATE pacientes SET direccion = 'Buena Vista' WHERE numero_expediente_fisico = '17508';
UPDATE pacientes SET direccion = 'Lopez Amax' WHERE numero_expediente_fisico = '17509';
UPDATE pacientes SET direccion = 'Enruchut' WHERE numero_expediente_fisico = '17510';
UPDATE pacientes SET direccion = 'Sach' WHERE numero_expediente_fisico = '17511';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17522';
UPDATE pacientes SET direccion = 'Llano Grande' WHERE numero_expediente_fisico = '17524';
UPDATE pacientes SET direccion = 'Sahtoc Beleju' WHERE numero_expediente_fisico = '17525';
UPDATE pacientes SET direccion = 'Uspantan' WHERE numero_expediente_fisico = '17526';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17527';
UPDATE pacientes SET direccion = 'Pinal' WHERE numero_expediente_fisico = '17528';
UPDATE pacientes SET direccion = 'Sahtoc El Pinal' WHERE numero_expediente_fisico = '17529';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17530';
UPDATE pacientes SET direccion = 'Espolon' WHERE numero_expediente_fisico = '17531';
UPDATE pacientes SET direccion = 'Conche' WHERE numero_expediente_fisico = '17532';
UPDATE pacientes SET direccion = 'San Vicente Pacaya' WHERE numero_expediente_fisico = '17533';
UPDATE pacientes SET direccion = 'El Salin' WHERE numero_expediente_fisico = '17534';
UPDATE pacientes SET direccion = 'Los Planes' WHERE numero_expediente_fisico = '17535';
UPDATE pacientes SET direccion = 'Chocorral' WHERE numero_expediente_fisico = '17536';
UPDATE pacientes SET direccion = 'Pancur' WHERE numero_expediente_fisico = '17537';
UPDATE pacientes SET direccion = 'Pujiul' WHERE numero_expediente_fisico = '17538';
UPDATE pacientes SET direccion = 'Chexiquin' WHERE numero_expediente_fisico = '17539';
UPDATE pacientes SET direccion = 'Buena Vista el Amay' WHERE numero_expediente_fisico = '17540';
UPDATE pacientes SET direccion = 'Chelas' WHERE numero_expediente_fisico = '17541';
UPDATE pacientes SET direccion = 'La Campana' WHERE numero_expediente_fisico = '17542';
UPDATE pacientes SET direccion = 'San Antonio Beleju' WHERE numero_expediente_fisico = '17543';
UPDATE pacientes SET direccion = 'Carrizal' WHERE numero_expediente_fisico = '17544';
UPDATE pacientes SET direccion = 'Xecaquic - Beleju' WHERE numero_expediente_fisico = '17545';
UPDATE pacientes SET direccion = 'San Sebastian' WHERE numero_expediente_fisico = '17546';
UPDATE pacientes SET direccion = 'Xecaquic' WHERE numero_expediente_fisico = '17547';
UPDATE pacientes SET direccion = 'Jumuc' WHERE numero_expediente_fisico = '17548';
UPDATE pacientes SET direccion = 'Portal' WHERE numero_expediente_fisico = '17550';
UPDATE pacientes SET direccion = 'Chixoy' WHERE numero_expediente_fisico = '17551';
UPDATE pacientes SET direccion = 'Laruz' WHERE numero_expediente_fisico = '17552';
UPDATE pacientes SET direccion = 'Joyabaj' WHERE numero_expediente_fisico = '17553';
UPDATE pacientes SET direccion = 'Beleju' WHERE numero_expediente_fisico = '17556';
UPDATE pacientes SET direccion = 'Agua Zarca' WHERE numero_expediente_fisico = '17557';
UPDATE pacientes SET direccion = 'Beleju' WHERE numero_expediente_fisico = '17558';
UPDATE pacientes SET direccion = 'Cebollin' WHERE numero_expediente_fisico = '17559';
UPDATE pacientes SET direccion = 'Jumuc' WHERE numero_expediente_fisico = '17560';
UPDATE pacientes SET direccion = 'El Palmar' WHERE numero_expediente_fisico = '17561';
UPDATE pacientes SET direccion = 'El Pinal' WHERE numero_expediente_fisico = '17562';
UPDATE pacientes SET direccion = 'Beleju' WHERE numero_expediente_fisico = '17563';
UPDATE pacientes SET direccion = 'Chicaman' WHERE numero_expediente_fisico = '17564';
UPDATE pacientes SET direccion = 'Clinica Los Calel' WHERE numero_expediente_fisico = '17565';
UPDATE pacientes SET direccion = 'Sobre Soca' WHERE numero_expediente_fisico = '17566';
UPDATE pacientes SET direccion = 'Coban, A.V.' WHERE numero_expediente_fisico = '17567';
UPDATE pacientes SET direccion = 'El Amay' WHERE numero_expediente_fisico = '17568';

COMMIT;
