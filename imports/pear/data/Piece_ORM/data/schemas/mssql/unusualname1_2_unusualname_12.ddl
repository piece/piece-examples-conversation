-- $Id: unusualname1_2_unusualname_12.ddl 328 2008-03-30 10:48:06Z iteman $

CREATE TABLE unusualname1_2_unusualname_12 (
  id int IDENTITY (1,1) NOT NULL,
  unusualname1_2_id int NOT NULL,
  unusualname_12_id int NOT NULL,
  CONSTRAINT PK_unusualname1_2_unusualname_12 PRIMARY KEY CLUSTERED (id ASC) WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
)

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */

