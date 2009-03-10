-- $Id$

CREATE TABLE orders (
  id serial,
  main integer NOT NULL,
  side integer NOT NULL,
  rdate timestamp with time zone NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY(id)
);

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
