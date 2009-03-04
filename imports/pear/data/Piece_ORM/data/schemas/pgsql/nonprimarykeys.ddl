-- $Id: nonprimarykeys.ddl 325 2008-03-30 10:17:56Z iteman $

CREATE TABLE nonprimarykeys (
  member_id int4 NOT NULL,
  service_id int4 NOT NULL,
  point int4 NOT NULL DEFAULT '0',
  created_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  updated_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  UNIQUE (member_id, service_id)
);

ALTER TABLE nonprimarykeys OWNER TO piece;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
