-- $Id: compositeprimarykey.ddl 328 2008-03-30 10:48:06Z iteman $

CREATE TABLE compositeprimarykey (
	album varchar (255) COLLATE Japanese_CI_AS NOT NULL,
	artist varchar (255) COLLATE Japanese_CI_AS NOT NULL,
	track int NOT NULL,
	song [varchar] (255) COLLATE Japanese_CI_AS NOT NULL,
  CONSTRAINT PK_compositeprimarykey PRIMARY KEY CLUSTERED (album ASC, artist ASC, track ASC) WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
)

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
