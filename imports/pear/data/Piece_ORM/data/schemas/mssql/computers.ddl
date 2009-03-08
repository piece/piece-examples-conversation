-- $Id: computers.ddl 328 2008-03-30 10:48:06Z iteman $

CREATE TABLE computers (
  id int IDENTITY (1,1) NOT NULL,
  name varchar (255) COLLATE Japanese_CI_AS NOT NULL,
  employees_id int NULL,
  created_at datetime NOT NULL CONSTRAINT DF_computers_created_at DEFAULT (getdate()),
  updated_at datetime NOT NULL CONSTRAINT DF_computers_updated_at DEFAULT (getdate()),
  CONSTRAINT PK_computers PRIMARY KEY CLUSTERED (id ASC) WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
)

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
