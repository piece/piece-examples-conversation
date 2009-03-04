-- $Id: unusualname1_2.ddl 324 2008-03-30 09:53:58Z iteman $

CREATE TABLE unusualname1_2 (
  id serial,
  name varchar (255) NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE unusualname1_2 OWNER TO piece;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
