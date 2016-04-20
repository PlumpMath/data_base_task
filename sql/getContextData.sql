CREATE DEFINER = 'root'@'localhost'
PROCEDURE adm_delinq.getContextData(IN mro int, IN mon int, IN article int, IN nsi5 int, IN year int, IN forma int, IN operation int, IN cumulative int)
  READS SQL DATA
BEGIN
  DROP TABLE IF EXISTS temp;

  /**************************************************************************
    name: getCount
    description: Get count all of  documents 
    parameters:
              mro-region,
              mon-month of report,
              article-article,
              nsi5- list № 5,
              year - year of report,
              forma - identoficator  of the forma,
              operation-kind of operation,
              cumulative-cumulative data 
    
    author: Kazun A.S.
    version: 1.0  24.03.2016
    last edition: 18.04.2016
    
    **************************************************************************/
  IF forma = 1 THEN
    SET @act_sel1 := 2;
    SET @act_sel2 := 2;
  ELSE
    SET @act_sel1 := 1;
    SET @act_sel2 := 3;
  END IF;
  SET @mon1 = (cumulative - 1) * 3;
  SET @mon2 = cumulative * 3;

  IF operation < 8 THEN
    CREATE TEMPORARY TABLE temp (
      reg_num varchar(50),
      fio_penalized varchar(50)
    );
  ELSE
    CREATE TEMPORARY TABLE temp (
      reg_num varchar(50),
      fio_penalized varchar(50),
      summa double
    );

  END IF;

  #start opertaion 1----------------------------------------------------------------------------------------------------------------------------------------------
  IF operation = 1 THEN
    IF article = 3
      AND nsi5 = 2 THEN
      #start article = 3----------------------------------------------------------------------------------------------------------------------------------------------
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          b_pr.reg_num,
          pr.fio_penalized
        FROM adm_delinq.act act_pr
          INNER JOIN adm_delinq.book b_pr
            ON act_pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.prepare pr
            ON pr.doc_id = b_pr.id
        WHERE (act_pr.nsi_2 = @act_sel1
        OR act_pr.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) > @mon1
        AND MONTH(b_pr.reg_date) <= @mon2))
        AND MONTH(b_pr.reg_date) != 0
        AND year(b_pr.reg_date) = year
        AND pr.statute_num > 0
        AND (b_pr.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_pr.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));
    END IF;

    #end article = 3_________________________________________________________________________________________________________________________________________



    #----------------------------------
    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        reg_num,
        fio_penalized
      FROM (SELECT
          b_p.reg_num AS reg_num,
          p.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_p
          INNER JOIN adm_delinq.book b_p
            ON act_p.doc_id = b_p.id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = b_p.id
        WHERE (act_p.nsi_2 = @act_sel1
        OR act_p.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_p.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_p.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_p.reg_date) > @mon1
        AND MONTH(b_p.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND MONTH(b_p.reg_date) != 0
        AND year(b_p.reg_date) = year
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))

        UNION ALL
        SELECT
          b_t.reg_num AS reg_num,
          t.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_t
          INNER JOIN adm_delinq.book b_t
            ON act_t.doc_id = b_t.id
          INNER JOIN adm_delinq.termination t
            ON t.doc_id = b_t.id
        WHERE (act_t.nsi_2 = @act_sel1
        OR act_t.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_t.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_t.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_t.reg_date) > @mon1
        AND MONTH(b_t.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND t.nsi_7 IN (1, 2))
        OR t.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND t.nsi_5 IN (1, 2))
        OR t.nsi_5 = nsi5)
        AND MONTH(b_t.reg_date) != 0
        AND year(b_t.reg_date) = year
        AND (b_t.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_t.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))) AS res);



    #__________________________________




    IF forma = 2 THEN
      #start forma = 2----------------------------------------------------------------------------------------------------------------------------------------------
      IF article = 3
        AND nsi5 = 2 THEN
        #start article = 3----------------------------------------------------------------------------------------------------------------------------------------------
        INSERT INTO temp (reg_num, fio_penalized)
          (SELECT
            b_pr.reg_num,
            pr.fio_penalized
          FROM adm_delinq.msg m_pr
            INNER JOIN adm_delinq.book b_pr
              ON m_pr.doc_id = b_pr.id
            INNER JOIN adm_delinq.prepare pr
              ON pr.doc_id = b_pr.id
            LEFT JOIN adm_delinq.act act_p
              ON m_pr.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) > @mon1
          AND MONTH(b_pr.reg_date) <= @mon2))
          AND MONTH(b_pr.reg_date) != 0
          AND year(b_pr.reg_date) = year
          AND pr.statute_num > 0
          AND (b_pr.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_pr.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))));
      END IF;
      #end article = 3_________________________________________________________________________________________________________________________________________
      #----------------------------------
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          reg_num,
          fio_penalized
        FROM (SELECT
            b_p.reg_num AS reg_num,
            p.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_p
            INNER JOIN adm_delinq.book b_p
              ON m_p.doc_id = b_p.id
            INNER JOIN adm_delinq.penalty p
              ON p.doc_id = b_p.id
            LEFT JOIN adm_delinq.act act_p
              ON m_p.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_p.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_p.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_p.reg_date) > @mon1
          AND MONTH(b_p.reg_date) <= @mon2))

          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND p.nsi_7 IN (1, 2))
          OR p.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND p.nsi_5 IN (1, 2))
          OR p.nsi_5 = nsi5)
          AND MONTH(b_p.reg_date) != 0
          AND year(b_p.reg_date) = year
          AND (b_p.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_p.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))

          UNION ALL
          SELECT
            b_t.reg_num AS reg_num,
            t.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_t
            INNER JOIN adm_delinq.book b_t
              ON m_t.doc_id = b_t.id
            INNER JOIN adm_delinq.termination t
              ON t.doc_id = b_t.id
            LEFT JOIN adm_delinq.act act_p
              ON m_t.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_t.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_t.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_t.reg_date) > @mon1
          AND MONTH(b_t.reg_date) <= @mon2))

          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND t.nsi_7 IN (1, 2))
          OR t.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND t.nsi_5 IN (1, 2))
          OR t.nsi_5 = nsi5)
          AND MONTH(b_t.reg_date) != 0
          AND year(b_t.reg_date) = year
          AND (b_t.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_t.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))) AS res);
    #__________________________________
    END IF;


  #end forma = 2_________________________________________________________________________________________________________________________________________
  #end operation =1________________________________________________________________________________________________________________________________________


  ELSEIF operation = 2 THEN

    #start opertaion 2----------------------------------------------------------------------------------------------------------------------------------------------

    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        reg_num,
        fio_penalized
      FROM (SELECT
          b_pr.reg_num AS reg_num,
          p.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_t
          INNER JOIN adm_delinq.book b_pr
            ON act_t.doc_id = b_pr.id
          INNER JOIN adm_delinq.prepare pr
            ON pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = b_pr.id
        WHERE (act_t.nsi_2 = @act_sel1
        OR act_t.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) > @mon1
        AND MONTH(b_pr.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND MONTH(b_pr.reg_date) != 0
        AND year(b_pr.reg_date) = year
        AND pr.protocol_num != 0 # operation 2
        AND (b_pr.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_pr.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))
        UNION ALL
        SELECT
          b_pr.reg_num AS reg_num,
          t.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_t
          INNER JOIN adm_delinq.book b_pr
            ON act_t.doc_id = b_pr.id
          INNER JOIN adm_delinq.prepare pr
            ON pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.termination t
            ON t.doc_id = b_pr.id
        WHERE (act_t.nsi_2 = @act_sel1
        OR act_t.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) > @mon1
        AND MONTH(b_pr.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND t.nsi_7 IN (1, 2))
        OR t.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND t.nsi_5 IN (1, 2))
        OR t.nsi_5 = nsi5)
        AND MONTH(b_pr.reg_date) != 0
        AND year(b_pr.reg_date) = year
        AND pr.protocol_num != 0
        AND (b_pr.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_pr.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))) AS res);


    #start forma = 2----------------------------------------------------------------------------------------------------------------------------------------------
    IF forma = 2 THEN
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          reg_num,
          fio_penalized
        FROM (SELECT
            b_p.reg_num AS reg_num,
            p.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_p
            INNER JOIN adm_delinq.book b_p
              ON m_p.doc_id = b_p.id
            INNER JOIN adm_delinq.prepare pr
              ON pr.doc_id = b_p.id
            INNER JOIN adm_delinq.penalty p
              ON p.doc_id = b_p.id
            LEFT JOIN adm_delinq.act act_p
              ON m_p.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_p.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_p.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_p.reg_date) > @mon1
          AND MONTH(b_p.reg_date) <= @mon2))
          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND p.nsi_7 IN (1, 2))
          OR p.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND p.nsi_5 IN (1, 2))
          OR p.nsi_5 = nsi5)
          AND MONTH(b_p.reg_date) != 0
          AND year(b_p.reg_date) = year
          AND pr.protocol_num != 0
          AND (b_p.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_p.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))
          UNION ALL
          SELECT
            b_t.reg_num AS reg_num,
            t.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_t
            INNER JOIN adm_delinq.book b_t
              ON m_t.doc_id = b_t.id
            INNER JOIN adm_delinq.prepare pr
              ON pr.doc_id = b_t.id
            INNER JOIN adm_delinq.termination t
              ON t.doc_id = b_t.id
            LEFT JOIN adm_delinq.act act_p
              ON m_t.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_t.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_t.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_t.reg_date) > @mon1
          AND MONTH(b_t.reg_date) <= @mon2))
          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND t.nsi_7 IN (1, 2))
          OR t.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND t.nsi_5 IN (1, 2))
          OR t.nsi_5 = nsi5)
          AND MONTH(b_t.reg_date) != 0
          AND year(b_t.reg_date) = year
          AND pr.protocol_num != 0
          AND (b_t.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_t.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))) AS res);
    END IF;
  #end forma =2________________________________________________________________________________________________________________________________________
  #end operation =2________________________________________________________________________________________________________________________________________



  ELSEIF operation = 3 THEN
    #start opertaion 3----------------------------------------------------------------------------------------------------------------------------------------------
    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        b_p.reg_num,
        p.fio_penalized
      FROM adm_delinq.act act_p
        INNER JOIN adm_delinq.book b_p
          ON act_p.doc_id = b_p.id
        INNER JOIN adm_delinq.penalty p
          ON p.doc_id = b_p.id
        INNER JOIN adm_delinq.review r
          ON r.doc_id = b_p.id
      WHERE (act_p.nsi_2 = @act_sel1
      OR act_p.nsi_2 = @act_sel2)
      AND ((IF(cumulative = 0, TRUE, FALSE)
      AND MONTH(b_p.reg_date) = mon)
      OR (IF(cumulative = 11, TRUE, FALSE)
      AND MONTH(b_p.reg_date) <= mon)
      OR (IF(cumulative = 1
      || cumulative = 2
      || cumulative = 3
      || cumulative = 4, TRUE, FALSE)
      AND MONTH(b_p.reg_date) > @mon1
      AND MONTH(b_p.reg_date) <= @mon2))
      AND MONTH(b_p.reg_date) != 0
      AND year(b_p.reg_date) = year
      AND ((IF(article = 1
      || article = 2, TRUE, FALSE)
      AND p.nsi_7 IN (1, 2))
      OR p.nsi_7 = article)
      AND ((IF(nsi5 = 2, TRUE, FALSE)
      AND p.nsi_5 IN (1, 2))
      OR p.nsi_5 = nsi5)
      AND r.nsi_11 IN (1, 2)
      AND (b_p.inspector_id
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))
      OR b_p.prepar_insp
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))));
    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        b_p.reg_num,
        t.fio_penalized
      FROM adm_delinq.act act_p
        INNER JOIN adm_delinq.book b_p
          ON act_p.doc_id = b_p.id
        INNER JOIN adm_delinq.termination t
          ON t.doc_id = b_p.id
        INNER JOIN adm_delinq.review r
          ON r.doc_id = b_p.id
      WHERE (act_p.nsi_2 = @act_sel1
      OR act_p.nsi_2 = @act_sel2)
      AND ((IF(cumulative = 0, TRUE, FALSE)
      AND MONTH(b_p.reg_date) = mon)
      OR (IF(cumulative = 11, TRUE, FALSE)
      AND MONTH(b_p.reg_date) <= mon)
      OR (IF(cumulative = 1
      || cumulative = 2
      || cumulative = 3
      || cumulative = 4, TRUE, FALSE)
      AND MONTH(b_p.reg_date) > @mon1
      AND MONTH(b_p.reg_date) <= @mon2))
      AND MONTH(b_p.reg_date) != 0
      AND year(b_p.reg_date) = year
      AND ((IF(article = 1
      || article = 2, TRUE, FALSE)
      AND t.nsi_7 IN (1, 2))
      OR t.nsi_7 = article)
      AND ((IF(nsi5 = 2, TRUE, FALSE)
      AND t.nsi_5 IN (1, 2))
      OR t.nsi_5 = nsi5)
      AND r.nsi_11 IN (1, 2)
      AND (b_p.inspector_id
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))
      OR b_p.prepar_insp
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))));


    #IF forma=2----------------------------------------------------------------------------------------------------------------------------------------------

    IF forma = 2 THEN
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          b_p.reg_num,
          p.fio_penalized
        FROM adm_delinq.msg m_p
          INNER JOIN adm_delinq.book b_p
            ON m_p.doc_id = b_p.id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = b_p.id
          INNER JOIN adm_delinq.review r
            ON r.doc_id = b_p.id
          LEFT JOIN adm_delinq.act act_p
            ON act_p.doc_id = m_p.doc_id
        WHERE act_p.doc_id IS NULL

        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_p.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_p.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_p.reg_date) > @mon1
        AND MONTH(b_p.reg_date) <= @mon2))
        AND MONTH(b_p.reg_date) != 0
        AND year(b_p.reg_date) = year
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND r.nsi_11 IN (1, 2)
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          b_p.reg_num,
          t.fio_penalized
        FROM adm_delinq.msg m_p
          INNER JOIN adm_delinq.book b_p
            ON m_p.doc_id = b_p.id
          INNER JOIN adm_delinq.termination t
            ON t.doc_id = b_p.id
          INNER JOIN adm_delinq.review r
            ON r.doc_id = b_p.id
          LEFT JOIN adm_delinq.act act_p
            ON act_p.doc_id = m_p.doc_id
        WHERE act_p.doc_id IS NULL
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_p.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_p.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_p.reg_date) > @mon1
        AND MONTH(b_p.reg_date) <= @mon2))
        AND MONTH(b_p.reg_date) != 0
        AND year(b_p.reg_date) = year
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND t.nsi_7 IN (1, 2))
        OR t.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND t.nsi_5 IN (1, 2))
        OR t.nsi_5 = nsi5)
        AND r.nsi_11 IN (1, 2)
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));

    END IF;
  #end forma=2________________________________________________________________________________________________________________________________________


  #end operation =3________________________________________________________________________________________________________________________________________
  ELSEIF operation = 4 THEN
    #start opertaion 4----------------------------------------------------------------------------------------------------------------------------------------------

    IF article = 3
      AND nsi5 = 2 THEN

      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          b_pr.reg_num,
          pr.fio_penalized
        FROM adm_delinq.act act_pr
          INNER JOIN adm_delinq.book b_pr
            ON act_pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.prepare pr
            ON pr.doc_id = b_pr.id
        WHERE (act_pr.nsi_2 = @act_sel1
        OR act_pr.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) > @mon1
        AND MONTH(b_pr.reg_date) <= @mon2))
        AND MONTH(b_pr.reg_date) != 0
        AND year(b_pr.reg_date) = year
        AND pr.statute_num > 0
        AND (b_pr.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_pr.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));

    END IF;

    #--------------------------------------------------------------
    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        reg_num,
        fio_penalized
      FROM (SELECT
          b_p.reg_num AS reg_num,
          p.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_p
          INNER JOIN adm_delinq.book b_p
            ON act_p.doc_id = b_p.id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = b_p.id
        WHERE (act_p.nsi_2 = @act_sel1
        OR act_p.nsi_2 = @act_sel2)
        AND p.nsi_4 = 1
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_p.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_p.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_p.reg_date) > @mon1
        AND MONTH(b_p.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND MONTH(b_p.reg_date) != 0
        AND year(b_p.reg_date) = year
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))
        UNION ALL
        SELECT
          b_t.reg_num AS reg_num,
          t.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_t
          INNER JOIN adm_delinq.book b_t
            ON act_t.doc_id = b_t.id
          INNER JOIN adm_delinq.termination t
            ON t.doc_id = b_t.id
        WHERE (act_t.nsi_2 = @act_sel1
        OR act_t.nsi_2 = @act_sel2)
        AND t.nsi_4 = 1
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_t.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_t.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_t.reg_date) > @mon1
        AND MONTH(b_t.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND t.nsi_7 IN (1, 2))
        OR t.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND t.nsi_5 IN (1, 2))
        OR t.nsi_5 = nsi5)
        AND MONTH(b_t.reg_date) != 0
        AND year(b_t.reg_date) = year
        AND (b_t.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_t.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))) AS res);


    #______________________________________________________________

    IF forma = 2 THEN
      #start forma = 2----------------------------------------------------------------------------------------------------------------------------------------------
      IF article = 3
        AND nsi5 = 2 THEN

        #start article = 3----------------------------------------------------------------------------------------------------------------------------------------------

        INSERT INTO temp (reg_num, fio_penalized)
          (SELECT
            b_pr.reg_num,
            pr.fio_penalized
          FROM adm_delinq.msg m_pr
            INNER JOIN adm_delinq.book b_pr
              ON m_pr.doc_id = b_pr.id
            INNER JOIN adm_delinq.prepare pr
              ON pr.doc_id = b_pr.id
            LEFT JOIN adm_delinq.act act_p
              ON m_pr.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) > @mon1
          AND MONTH(b_pr.reg_date) <= @mon2))
          AND MONTH(b_pr.reg_date) != 0
          AND year(b_pr.reg_date) = year
          AND pr.statute_num > 0
          AND (b_pr.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_pr.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))));
      END IF;

      #end article = 3_________________________________________________________________________________________________________________________________________
      #--------------------------------------------------------------
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          reg_num,
          fio_penalized
        FROM (SELECT
            b_p.reg_num AS reg_num,
            p.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_p
            INNER JOIN adm_delinq.book b_p
              ON m_p.doc_id = b_p.id
            INNER JOIN adm_delinq.penalty p
              ON p.doc_id = b_p.id
            LEFT JOIN adm_delinq.act act_p
              ON m_p.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND p.nsi_4 = 1
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_p.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_p.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_p.reg_date) > @mon1
          AND MONTH(b_p.reg_date) <= @mon2))
          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND p.nsi_7 IN (1, 2))
          OR p.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND p.nsi_5 IN (1, 2))
          OR p.nsi_5 = nsi5)
          AND MONTH(b_p.reg_date) != 0
          AND year(b_p.reg_date) = year
          AND (b_p.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_p.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))
          UNION ALL
          SELECT
            b_t.reg_num AS reg_num,
            t.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_t
            INNER JOIN adm_delinq.book b_t
              ON m_t.doc_id = b_t.id
            INNER JOIN adm_delinq.termination t
              ON t.doc_id = b_t.id
            LEFT JOIN adm_delinq.act act_p
              ON m_t.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND t.nsi_4 = 1
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_t.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_t.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_t.reg_date) > @mon1
          AND MONTH(b_t.reg_date) <= @mon2))
          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND t.nsi_7 IN (1, 2))
          OR t.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND t.nsi_5 IN (1, 2))
          OR t.nsi_5 = nsi5)
          AND MONTH(b_t.reg_date) != 0
          AND year(b_t.reg_date) = year
          AND (b_t.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_t.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))) AS res);

    #______________________________________________________________
    END IF;
  #end forma =2________________________________________________________________________________________________________________________________________
  #end operation =4________________________________________________________________________________________________________________________________________

  ELSEIF operation = 5 THEN
    #start opertaion 5----------------------------------------------------------------------------------------------------------------------------------------------
    IF article = 3
      AND nsi5 = 2 THEN
      #end article = 3----------------------------------------------------------------------------------------------------------------------------------------------
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          b_pr.reg_num,
          pr.fio_penalized
        FROM adm_delinq.act act_pr
          INNER JOIN adm_delinq.book b_pr
            ON act_pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.prepare pr
            ON pr.doc_id = b_pr.id
        WHERE (act_pr.nsi_2 = @act_sel1
        OR act_pr.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) > @mon1
        AND MONTH(b_pr.reg_date) <= @mon2))
        AND MONTH(b_pr.reg_date) != 0
        AND year(b_pr.reg_date) = year
        AND pr.statute_num > 0
        AND (b_pr.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_pr.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));
    END IF;
    #end article = 3_________________________________________________________________________________________________________________________________________
    #-----------------------------------------------------------------------


    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        reg_num,
        fio_penalized
      FROM (SELECT
          b_p.reg_num AS reg_num,
          p.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_p
          INNER JOIN adm_delinq.book b_p
            ON act_p.doc_id = b_p.id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = b_p.id
        WHERE (act_p.nsi_2 = @act_sel1
        OR act_p.nsi_2 = @act_sel2)
        AND p.nsi_4 = 1
        AND p.nsi_8 = 1
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_p.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_p.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_p.reg_date) > @mon1
        AND MONTH(b_p.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND MONTH(b_p.reg_date) != 0
        AND year(b_p.reg_date) = year
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))) AS res);

    #_______________________________________________________________________


    IF forma = 2 THEN
      #start forma = 2----------------------------------------------------------------------------------------------------------------------------------------------
      IF article = 3
        AND nsi5 = 2 THEN
        #end article = 3----------------------------------------------------------------------------------------------------------------------------------------------
        INSERT INTO temp (reg_num, fio_penalized)
          (SELECT
            b_pr.reg_num,
            pr.fio_penalized
          FROM adm_delinq.msg m_pr
            INNER JOIN adm_delinq.book b_pr
              ON m_pr.doc_id = b_pr.id
            INNER JOIN adm_delinq.prepare pr
              ON pr.doc_id = b_pr.id
            LEFT JOIN adm_delinq.act act_p
              ON m_pr.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) > @mon1
          AND MONTH(b_pr.reg_date) <= @mon2))
          AND MONTH(b_pr.reg_date) != 0
          AND year(b_pr.reg_date) = year
          AND pr.statute_num > 0
          AND (b_pr.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_pr.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))));
      END IF;

      #end article = 3_________________________________________________________________________________________________________________________________________

      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          reg_num,
          fio_penalized
        FROM (SELECT
            b_p.reg_num AS reg_num,
            p.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_p
            INNER JOIN adm_delinq.book b_p
              ON m_p.doc_id = b_p.id
            INNER JOIN adm_delinq.penalty p
              ON p.doc_id = b_p.id
            LEFT JOIN adm_delinq.act act_p
              ON m_p.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND p.nsi_4 = 1
          AND p.nsi_8 = 1
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_p.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_p.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_p.reg_date) > @mon1
          AND MONTH(b_p.reg_date) <= @mon2))
          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND p.nsi_7 IN (1, 2))
          OR p.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND p.nsi_5 IN (1, 2))
          OR p.nsi_5 = nsi5)
          AND MONTH(b_p.reg_date) != 0
          AND year(b_p.reg_date) = year
          AND (b_p.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_p.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))) AS res);

    END IF;


  #end forma =2________________________________________________________________________________________________________________________________________
  #end operation =5________________________________________________________________________________________________________________________________________

  ELSEIF operation = 6 THEN
    #start opertaion 6----------------------------------------------------------------------------------------------------------------------------------------------
    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        reg_num,
        fio_penalized
      FROM (SELECT
          b_p.reg_num AS reg_num,
          p.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_p
          INNER JOIN adm_delinq.book b_p
            ON act_p.doc_id = b_p.id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = b_p.id
        WHERE (act_p.nsi_2 = @act_sel1
        OR act_p.nsi_2 = @act_sel2)
        AND p.nsi_4 = 1
        AND p.nsi_8 = 2
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_p.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_p.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_p.reg_date) > @mon1
        AND MONTH(b_p.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND MONTH(b_p.reg_date) != 0
        AND year(b_p.reg_date) = year
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))) AS res);

    IF forma = 2 THEN
      #start forma = 2----------------------------------------------------------------------------------------------------------------------------------------------
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          reg_num,
          fio_penalized
        FROM (SELECT
            b_p.reg_num AS reg_num,
            p.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_p
            INNER JOIN adm_delinq.book b_p
              ON m_p.doc_id = b_p.id
            INNER JOIN adm_delinq.penalty p
              ON p.doc_id = b_p.id
            LEFT JOIN adm_delinq.act act_p
              ON m_p.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND p.nsi_4 = 1
          AND p.nsi_8 = 2
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_p.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_p.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_p.reg_date) > @mon1
          AND MONTH(b_p.reg_date) <= @mon2))
          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND p.nsi_7 IN (1, 2))
          OR p.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND p.nsi_5 IN (1, 2))
          OR p.nsi_5 = nsi5)
          AND MONTH(b_p.reg_date) != 0
          AND year(b_p.reg_date) = year
          AND (b_p.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_p.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))) AS res);

    END IF;
  #end forma =2________________________________________________________________________________________________________________________________________
  #end operation =6________________________________________________________________________________________________________________________________________



  ELSEIF operation = 7 THEN
    #start opertaion 7----------------------------------------------------------------------------------------------------------------------------------------------
    INSERT INTO temp (reg_num, fio_penalized)
      (SELECT
        reg_num,
        fio_penalized
      FROM (SELECT
          b_t.reg_num AS reg_num,
          t.fio_penalized AS fio_penalized
        FROM adm_delinq.act act_t
          INNER JOIN adm_delinq.book b_t
            ON act_t.doc_id = b_t.id
          INNER JOIN adm_delinq.termination t
            ON t.doc_id = b_t.id
        WHERE (act_t.nsi_2 = @act_sel1
        OR act_t.nsi_2 = @act_sel2)
        AND t.nsi_4 = 1
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_t.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_t.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_t.reg_date) > @mon1
        AND MONTH(b_t.reg_date) <= @mon2))
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND t.nsi_7 IN (1, 2))
        OR t.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND t.nsi_5 IN (1, 2))
        OR t.nsi_5 = nsi5)
        AND MONTH(b_t.reg_date) != 0
        AND year(b_t.reg_date) = year
        AND (b_t.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_t.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE))))) AS res);


    IF forma = 2 THEN
      #start forma = 2----------------------------------------------------------------------------------------------------------------------------------------------
      INSERT INTO temp (reg_num, fio_penalized)
        (SELECT
          reg_num,
          fio_penalized
        FROM (SELECT
            b_t.reg_num AS reg_num,
            t.fio_penalized AS fio_penalized
          FROM adm_delinq.msg m_t
            INNER JOIN adm_delinq.book b_t
              ON m_t.doc_id = b_t.id
            INNER JOIN adm_delinq.termination t
              ON t.doc_id = b_t.id
            LEFT JOIN adm_delinq.act act_t
              ON m_t.doc_id = act_t.doc_id
          WHERE act_t.doc_id IS NULL
          AND t.nsi_4 = 1
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_t.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_t.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_t.reg_date) > @mon1
          AND MONTH(b_t.reg_date) <= @mon2))
          AND ((IF(article = 1
          || article = 2, TRUE, FALSE)
          AND t.nsi_7 IN (1, 2))
          OR t.nsi_7 = article)
          AND ((IF(nsi5 = 2, TRUE, FALSE)
          AND t.nsi_5 IN (1, 2))
          OR t.nsi_5 = nsi5)
          AND MONTH(b_t.reg_date) != 0
          AND year(b_t.reg_date) = year
          AND (b_t.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_t.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE))))) AS res);

    END IF;
  #end forma =2________________________________________________________________________________________________________________________________________

  #end operation =7________________________________________________________________________________________________________________________________________
  #start operation 8-----------------------------------------------------------------------------------------------

  ELSEIF operation = 8 THEN

    # start article=3 and nsi5=2-----------------------------------------------------------------------------------------------

    IF article = 3
      AND nsi5 = 2 THEN
      INSERT INTO temp (reg_num, fio_penalized, summa)
        (SELECT
          b_pr.reg_num,
          pr.fio_penalized,
          pr.summa
        FROM adm_delinq.act act_pr
          INNER JOIN adm_delinq.book b_pr
            ON act_pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.prepare pr
            ON pr.doc_id = b_pr.id
        WHERE (act_pr.nsi_2 = @act_sel1
        OR act_pr.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_pr.reg_date) > @mon1
        AND MONTH(b_pr.reg_date) <= @mon2))
        AND MONTH(b_pr.reg_date) != 0
        AND year(b_pr.reg_date) = year
        AND pr.statute_num > 0
        AND (b_pr.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_pr.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));
    END IF;
    # end article=3 and nsi5=2-----------------------------------------------------------------------------------------------

    INSERT INTO temp (reg_num, fio_penalized, summa)
      (SELECT
        b_p.reg_num,
        p.fio_penalized,
        p.summa
      FROM adm_delinq.act act_p
        INNER JOIN adm_delinq.book b_p
          ON act_p.doc_id = b_p.id
        INNER JOIN adm_delinq.penalty p
          ON p.doc_id = b_p.id
      WHERE (act_p.nsi_2 = @act_sel1
      OR act_p.nsi_2 = @act_sel2)
      AND p.nsi_4 = 1
      AND p.nsi_8 = 1
      AND ((IF(cumulative = 0, TRUE, FALSE)
      AND MONTH(b_p.reg_date) = mon)
      OR (IF(cumulative = 11, TRUE, FALSE)
      AND MONTH(b_p.reg_date) <= mon)
      OR (IF(cumulative = 1
      || cumulative = 2
      || cumulative = 3
      || cumulative = 4, TRUE, FALSE)
      AND MONTH(b_p.reg_date) > @mon1
      AND MONTH(b_p.reg_date) <= @mon2))
      AND MONTH(b_p.reg_date) != 0
      AND year(b_p.reg_date) = year
      AND ((IF(article = 1
      || article = 2, TRUE, FALSE)
      AND p.nsi_7 IN (1, 2))
      OR p.nsi_7 = article)
      AND ((IF(nsi5 = 2, TRUE, FALSE)
      AND p.nsi_5 IN (1, 2))
      OR p.nsi_5 = nsi5)
      AND (b_p.inspector_id
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))
      OR b_p.prepar_insp
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))));




    # start forma=2-----------------------------------------------------------------------------------------------

    IF forma = 2 THEN
      # start article=3 and nsi5=2-----------------------------------------------------------------------------------------------
      IF article = 3
        AND nsi5 = 2 THEN
        INSERT INTO temp (reg_num, fio_penalized, summa)
          (SELECT
            b_pr.reg_num,
            pr.fio_penalized,
            pr.summa
          FROM adm_delinq.msg m_pr
            INNER JOIN adm_delinq.book b_pr
              ON m_pr.doc_id = b_pr.id
            INNER JOIN adm_delinq.prepare pr
              ON pr.doc_id = b_pr.id
            LEFT JOIN adm_delinq.act act_p
              ON m_pr.doc_id = act_p.doc_id
          WHERE act_p.doc_id IS NULL
          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(b_pr.reg_date) > @mon1
          AND MONTH(b_pr.reg_date) <= @mon2))
          AND MONTH(b_pr.reg_date) != 0
          AND year(b_pr.reg_date) = year
          AND pr.statute_num > 0
          AND (b_pr.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_pr.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))));
      END IF;
      # end article=3 and nsi5=2-----------------------------------------------------------------------------------------------

      INSERT INTO temp (reg_num, fio_penalized, summa)
        (SELECT
          b_p.reg_num,
          p.fio_penalized,
          p.summa
        FROM adm_delinq.msg m_p
          INNER JOIN adm_delinq.book b_p
            ON m_p.doc_id = b_p.id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = b_p.id
          LEFT JOIN adm_delinq.act act_p
            ON m_p.doc_id = act_p.doc_id
        WHERE act_p.doc_id IS NULL
        AND p.nsi_4 = 1
        AND p.nsi_8 = 1
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(b_p.reg_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(b_p.reg_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(b_p.reg_date) > @mon1
        AND MONTH(b_p.reg_date) <= @mon2))
        AND MONTH(b_p.reg_date) != 0
        AND year(b_p.reg_date) = year
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));


    END IF;

  # end forma=2--------------------------------------------------------------------------------------------------
  #end operation 8-----------------------------------------------------------------------------------------------


  #start operation 9---------------------------------------------------------------------------------------------

  ELSEIF operation = 9 THEN
    IF article = 3
      AND nsi5 = 2 THEN
      INSERT INTO temp (reg_num, fio_penalized, summa)
        (SELECT
          b_pr.reg_num,
          pr.fio_penalized,
          pr.summa
        FROM adm_delinq.act act_pr
          INNER JOIN adm_delinq.book b_pr
            ON act_pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.prepare pr
            ON pr.doc_id = b_pr.id
          INNER JOIN adm_delinq.inform inf
            ON inf.doc_id = b_pr.id
        WHERE (act_pr.nsi_2 = @act_sel1
        OR act_pr.nsi_2 = @act_sel2)
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(inf.receipt_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(inf.receipt_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(inf.receipt_date) > @mon1
        AND MONTH(inf.receipt_date) <= @mon2))
        AND MONTH(inf.receipt_date) != 0
        AND year(inf.receipt_date) = year
        AND pr.statute_num > 0
        AND (b_pr.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_pr.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));
    END IF;

    INSERT INTO temp (reg_num, fio_penalized, summa)
      (SELECT
        b_p.reg_num,
        p.fio_penalized,
        p.summa
      FROM adm_delinq.act act_p
        INNER JOIN adm_delinq.book b_p
          ON act_p.doc_id = b_p.id
        INNER JOIN adm_delinq.penalty p
          ON p.doc_id = b_p.id
        INNER JOIN adm_delinq.inform inf
          ON inf.doc_id = b_p.id
      WHERE (act_p.nsi_2 = @act_sel1
      OR act_p.nsi_2 = @act_sel2)
      AND p.nsi_4 = 1
      AND p.nsi_8 = 1
      AND ((IF(cumulative = 0, TRUE, FALSE)
      AND MONTH(inf.receipt_date) = mon)
      OR (IF(cumulative = 11, TRUE, FALSE)
      AND MONTH(inf.receipt_date) <= mon)
      OR (IF(cumulative = 1
      || cumulative = 2
      || cumulative = 3
      || cumulative = 4, TRUE, FALSE)
      AND MONTH(inf.receipt_date) > @mon1
      AND MONTH(inf.receipt_date) <= @mon2))
      AND MONTH(inf.receipt_date) != 0
      AND year(inf.receipt_date) = year
      AND ((IF(article = 1
      || article = 2, TRUE, FALSE)
      AND p.nsi_7 IN (1, 2))
      OR p.nsi_7 = article)
      AND ((IF(nsi5 = 2, TRUE, FALSE)
      AND p.nsi_5 IN (1, 2))
      OR p.nsi_5 = nsi5)
      AND (b_p.inspector_id
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))
      OR b_p.prepar_insp
      IN (SELECT
          id
        FROM adm_delinq.user
        WHERE id_inspection
        IN (SELECT
            id
          FROM adm_delinq.inspection
          WHERE id_mro = mro
          OR IF(mro = 4, TRUE, FALSE)))));

    # start forma=2-----------------------------------------------------------------------------------------------

    IF forma = 2 THEN
      IF article = 3
        AND nsi5 = 2 THEN
        INSERT INTO temp (reg_num, fio_penalized, summa)
          (SELECT
            b_pr.reg_num,
            pr.fio_penalized,
            pr.summa
          FROM adm_delinq.msg m_pr
            INNER JOIN adm_delinq.book b_pr
              ON b_pr.id = m_pr.doc_id
            INNER JOIN adm_delinq.prepare pr
              ON pr.doc_id = m_pr.doc_id
            INNER JOIN adm_delinq.inform inf
              ON inf.doc_id = m_pr.doc_id
            LEFT JOIN adm_delinq.act act_p
              ON act_p.doc_id = m_pr.doc_id
          WHERE act_p.doc_id IS NULL

          AND ((IF(cumulative = 0, TRUE, FALSE)
          AND MONTH(inf.receipt_date) = mon)
          OR (IF(cumulative = 11, TRUE, FALSE)
          AND MONTH(inf.receipt_date) <= mon)
          OR (IF(cumulative = 1
          || cumulative = 2
          || cumulative = 3
          || cumulative = 4, TRUE, FALSE)
          AND MONTH(inf.receipt_date) > @mon1
          AND MONTH(inf.receipt_date) <= @mon2))
          AND MONTH(inf.receipt_date) != 0
          AND year(inf.receipt_date) = year
          AND pr.statute_num > 0
          AND (b_pr.inspector_id
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))
          OR b_pr.prepar_insp
          IN (SELECT
              id
            FROM adm_delinq.user
            WHERE id_inspection
            IN (SELECT
                id
              FROM adm_delinq.inspection
              WHERE id_mro = mro
              OR IF(mro = 4, TRUE, FALSE)))));
      END IF;
      # end forma=2-----------------------------------------------------------------------------------------------


      INSERT INTO temp (reg_num, fio_penalized, summa)
        (SELECT
          b_p.reg_num,
          p.fio_penalized,
          p.summa
        FROM adm_delinq.msg m_p
          INNER JOIN adm_delinq.book b_p
            ON b_p.id = m_p.doc_id
          INNER JOIN adm_delinq.penalty p
            ON p.doc_id = m_p.doc_id
          INNER JOIN adm_delinq.inform inf
            ON inf.doc_id = m_p.doc_id
          LEFT JOIN adm_delinq.act act_p
            ON act_p.doc_id = m_p.doc_id
        WHERE act_p.doc_id IS NULL
        AND p.nsi_4 = 1
        AND p.nsi_8 = 1
        AND ((IF(cumulative = 0, TRUE, FALSE)
        AND MONTH(inf.receipt_date) = mon)
        OR (IF(cumulative = 11, TRUE, FALSE)
        AND MONTH(inf.receipt_date) <= mon)
        OR (IF(cumulative = 1
        || cumulative = 2
        || cumulative = 3
        || cumulative = 4, TRUE, FALSE)
        AND MONTH(inf.receipt_date) > @mon1
        AND MONTH(inf.receipt_date) <= @mon2))
        AND MONTH(inf.receipt_date) != 0
        AND year(inf.receipt_date) = year
        AND ((IF(article = 1
        || article = 2, TRUE, FALSE)
        AND p.nsi_7 IN (1, 2))
        OR p.nsi_7 = article)
        AND ((IF(nsi5 = 2, TRUE, FALSE)
        AND p.nsi_5 IN (1, 2))
        OR p.nsi_5 = nsi5)
        AND (b_p.inspector_id
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))
        OR b_p.prepar_insp
        IN (SELECT
            id
          FROM adm_delinq.user
          WHERE id_inspection
          IN (SELECT
              id
            FROM adm_delinq.inspection
            WHERE id_mro = mro
            OR IF(mro = 4, TRUE, FALSE)))));

    END IF;
  END IF;
  #end operation 9-----------------------------------------------------------------------------------------------

  SELECT
    *
  FROM temp;

  DROP TABLE IF EXISTS temp;
END