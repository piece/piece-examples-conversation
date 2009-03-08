-- $Id: departments.ddl 323 2008-03-30 09:53:38Z iteman $

CREATE TABLE departments (
  id serial,
  name varchar (255) NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  updated_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (id)
);

ALTER TABLE departments OWNER TO piece;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
