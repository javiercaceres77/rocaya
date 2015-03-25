# rocaya

to extract the users data
SELECT ur.user_id AS usuario, ur.climb_date AS fecha_escalada, ur.num_tries AS num_intentos, r.rname AS Ruta, s.sname AS Sector, c.cname AS Escuela, ct.desc_es AS Tipo_escalada
FROM users_routes ur
INNER JOIN routes r ON r.route_id = ur.route_id
INNER JOIN sectors s ON s.sector_id = r.sector_id
INNER JOIN crags c ON c.crag_id = r.crag_id
INNER JOIN climbs_types ct ON ct.climb_type_id = ur.climb_type
