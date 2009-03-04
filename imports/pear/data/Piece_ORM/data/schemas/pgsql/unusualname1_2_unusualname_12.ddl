-- $Id: unusualname1_2_unusualname_12.ddl 324 2008-03-30 09:53:58Z iteman $

CREATE TABLE unusualname1_2_unusualname_12 (
  id serial,
  unusualname1_2_id int4 NOT NULL,
  unusualname_12_id int4 NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE unusualname1_2_unusualname_12 OWNER TO piece;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
