-- $Id: employees_skills.ddl 323 2008-03-30 09:53:38Z iteman $

CREATE TABLE employees_skills (
  id serial,
  employees_id int4 NOT NULL,
  skills_id int4 NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  updated_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (id)
);

ALTER TABLE employees_skills OWNER TO piece;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
