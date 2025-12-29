-- Query SQL untuk mendapatkan total pasien (Si Carik + Manual Input)
-- Query ini menggabungkan $carik_data['total_pasien'] + $manual_data['total_pasien']
-- dengan deduplication NIK (hanya mengambil record terakhir per NIK)

-- ============================================
-- QUERY UNTUK SUPERADMIN (tanpa filter role)
-- ============================================
SELECT COUNT(DISTINCT p.id) as total_pasien
FROM pasiens p
INNER JOIN villages v ON p.village_id = v.id
INNER JOIN districts d ON v.district_id = d.id
INNER JOIN regencies r ON d.regency_id = r.id
INNER JOIN (
    SELECT nik, MAX(id) as latest_id
    FROM pasiens
    WHERE deleted_at IS NULL 
    AND nik IS NOT NULL
    GROUP BY nik
) latest ON p.id = latest.latest_id
WHERE (
    p.flag_sicarik = 1  -- Si Carik
    OR p.flag_sicarik = 0  -- Manual Input
    OR p.flag_sicarik IS NULL  -- Manual Input (default)
)
AND p.deleted_at IS NULL
AND p.village_id IS NOT NULL
AND p.nik IS NOT NULL;

-- ============================================
-- QUERY UNTUK SUPERADMIN dengan filter district_id
-- ============================================
SELECT COUNT(DISTINCT p.id) as total_pasien
FROM pasiens p
INNER JOIN villages v ON p.village_id = v.id
INNER JOIN districts d ON v.district_id = d.id
INNER JOIN regencies r ON d.regency_id = r.id
INNER JOIN (
    SELECT nik, MAX(id) as latest_id
    FROM pasiens
    WHERE deleted_at IS NULL 
    AND nik IS NOT NULL
    GROUP BY nik
) latest ON p.id = latest.latest_id
WHERE (
    p.flag_sicarik = 1  -- Si Carik
    OR p.flag_sicarik = 0  -- Manual Input
    OR p.flag_sicarik IS NULL  -- Manual Input (default)
)
AND p.deleted_at IS NULL
AND p.village_id IS NOT NULL
AND p.nik IS NOT NULL
AND d.id = ?;  -- district_id parameter

-- ============================================
-- QUERY UNTUK SUPERADMIN dengan filter village_id
-- ============================================
SELECT COUNT(DISTINCT p.id) as total_pasien
FROM pasiens p
INNER JOIN villages v ON p.village_id = v.id
INNER JOIN districts d ON v.district_id = d.id
INNER JOIN regencies r ON d.regency_id = r.id
INNER JOIN (
    SELECT nik, MAX(id) as latest_id
    FROM pasiens
    WHERE deleted_at IS NULL 
    AND nik IS NOT NULL
    GROUP BY nik
) latest ON p.id = latest.latest_id
WHERE (
    p.flag_sicarik = 1  -- Si Carik
    OR p.flag_sicarik = 0  -- Manual Input
    OR p.flag_sicarik IS NULL  -- Manual Input (default)
)
AND p.deleted_at IS NULL
AND p.village_id IS NOT NULL
AND p.nik IS NOT NULL
AND p.village_id = ?;  -- village_id parameter

-- ============================================
-- QUERY UNTUK PERAWAT/OPERATOR (filter by district)
-- ============================================
SELECT COUNT(DISTINCT p.id) as total_pasien
FROM pasiens p
INNER JOIN villages v ON p.village_id = v.id
INNER JOIN districts d ON v.district_id = d.id
INNER JOIN regencies r ON d.regency_id = r.id
INNER JOIN (
    SELECT nik, MAX(id) as latest_id
    FROM pasiens
    WHERE deleted_at IS NULL 
    AND nik IS NOT NULL
    GROUP BY nik
) latest ON p.id = latest.latest_id
WHERE (
    p.flag_sicarik = 1  -- Si Carik
    OR p.flag_sicarik = 0  -- Manual Input
    OR p.flag_sicarik IS NULL  -- Manual Input (default)
)
AND p.deleted_at IS NULL
AND p.village_id IS NOT NULL
AND p.nik IS NOT NULL
AND d.id = ?;  -- district_id dari user (dari pustu atau village)

-- ============================================
-- QUERY UNTUK SUDINKES (filter by regency_id)
-- ============================================
SELECT COUNT(DISTINCT p.id) as total_pasien
FROM pasiens p
INNER JOIN villages v ON p.village_id = v.id
INNER JOIN districts d ON v.district_id = d.id
INNER JOIN regencies r ON d.regency_id = r.id
INNER JOIN (
    SELECT nik, MAX(id) as latest_id
    FROM pasiens
    WHERE deleted_at IS NULL 
    AND nik IS NOT NULL
    GROUP BY nik
) latest ON p.id = latest.latest_id
WHERE (
    p.flag_sicarik = 1  -- Si Carik
    OR p.flag_sicarik = 0  -- Manual Input
    OR p.flag_sicarik IS NULL  -- Manual Input (default)
)
AND p.deleted_at IS NULL
AND p.village_id IS NOT NULL
AND p.nik IS NOT NULL
AND r.id = ?;  -- regency_id dari user

-- ============================================
-- QUERY UNTUK SUDINKES dengan filter district_id
-- ============================================
SELECT COUNT(DISTINCT p.id) as total_pasien
FROM pasiens p
INNER JOIN villages v ON p.village_id = v.id
INNER JOIN districts d ON v.district_id = d.id
INNER JOIN regencies r ON d.regency_id = r.id
INNER JOIN (
    SELECT nik, MAX(id) as latest_id
    FROM pasiens
    WHERE deleted_at IS NULL 
    AND nik IS NOT NULL
    GROUP BY nik
) latest ON p.id = latest.latest_id
WHERE (
    p.flag_sicarik = 1  -- Si Carik
    OR p.flag_sicarik = 0  -- Manual Input
    OR p.flag_sicarik IS NULL  -- Manual Input (default)
)
AND p.deleted_at IS NULL
AND p.village_id IS NOT NULL
AND p.nik IS NOT NULL
AND r.id = ?  -- regency_id dari user
AND d.id = ?;  -- district_id parameter

-- ============================================
-- QUERY UNTUK SUDINKES dengan filter village_id
-- ============================================
SELECT COUNT(DISTINCT p.id) as total_pasien
FROM pasiens p
INNER JOIN villages v ON p.village_id = v.id
INNER JOIN districts d ON v.district_id = d.id
INNER JOIN regencies r ON d.regency_id = r.id
INNER JOIN (
    SELECT nik, MAX(id) as latest_id
    FROM pasiens
    WHERE deleted_at IS NULL 
    AND nik IS NOT NULL
    GROUP BY nik
) latest ON p.id = latest.latest_id
WHERE (
    p.flag_sicarik = 1  -- Si Carik
    OR p.flag_sicarik = 0  -- Manual Input
    OR p.flag_sicarik IS NULL  -- Manual Input (default)
)
AND p.deleted_at IS NULL
AND p.village_id IS NOT NULL
AND p.nik IS NOT NULL
AND r.id = ?  -- regency_id dari user
AND p.village_id = ?;  -- village_id parameter

-- ============================================
-- QUERY GENERIC (dapat digunakan dengan parameter dinamis)
-- ============================================
-- Contoh penggunaan di PHP:
-- $sql = "
--     SELECT COUNT(DISTINCT p.id) as total_pasien
--     FROM pasiens p
--     INNER JOIN villages v ON p.village_id = v.id
--     INNER JOIN districts d ON v.district_id = d.id
--     INNER JOIN regencies r ON d.regency_id = r.id
--     INNER JOIN (
--         SELECT nik, MAX(id) as latest_id
--         FROM pasiens
--         WHERE deleted_at IS NULL 
--         AND nik IS NOT NULL
--         GROUP BY nik
--     ) latest ON p.id = latest.latest_id
--     WHERE (
--         p.flag_sicarik = 1
--         OR p.flag_sicarik = 0
--         OR p.flag_sicarik IS NULL
--     )
--     AND p.deleted_at IS NULL
--     AND p.village_id IS NOT NULL
--     AND p.nik IS NOT NULL
-- ";
-- 
-- $bindings = [];
-- 
-- // Tambahkan filter berdasarkan role
-- if ($user->role === 'sudinkes') {
--     $sql .= " AND r.id = ?";
--     $bindings[] = $user->regency_id;
-- } elseif (in_array($user->role, ['perawat', 'operator'])) {
--     $districtId = $this->getUserDistrictId($user);
--     if ($districtId) {
--         $sql .= " AND d.id = ?";
--         $bindings[] = $districtId;
--     }
-- }
-- 
-- // Tambahkan filter tambahan
-- if (!empty($filters['district_id'])) {
--     $sql .= " AND d.id = ?";
--     $bindings[] = $filters['district_id'];
-- }
-- if (!empty($filters['village_id'])) {
--     $sql .= " AND p.village_id = ?";
--     $bindings[] = $filters['village_id'];
-- }
-- 
-- $result = DB::selectOne($sql, $bindings);
-- $totalPasien = $result->total_pasien ?? 0;


