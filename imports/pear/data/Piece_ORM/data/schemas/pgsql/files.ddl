-- $Id: files.ddl 391 2008-06-03 18:35:37Z iteman $

CREATE TABLE files (
  id serial,
  document_body text,
  picture bytea,
  large_picture bytea,
  created_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  updated_at timestamp with time zone NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (id)
);

ALTER TABLE files OWNER TO piece;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
