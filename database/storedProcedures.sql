use cgnst;

DROP PROCEDURE IF EXISTS sp_medals;
DELIMITER $$
#in_discipline can be % for all disciplines
CREATE PROCEDURE sp_medals(IN in_discipline varchar(50))
BEGIN

SELECT name, surename,
GROUP_CONCAT(DISTINCT discipline SEPARATOR ', ') AS "disciplines",
count(*) AS "medals",
sum(case when place = 1 then 1 else 0 end) AS "Gold medals",
sum(case when place = 2 then 1 else 0 end) AS "Silber medals",
sum(case when place = 3 then 1 else 0 end) AS "Broze medals",
sum(4 - place) AS "score"
from wm WHERE discipline LIKE in_discipline AND place BETWEEN 1 AND 3
GROUP BY surename, name
ORDER BY score DESC;

END$$
DELIMITER ;

call sp_medals("500 road");