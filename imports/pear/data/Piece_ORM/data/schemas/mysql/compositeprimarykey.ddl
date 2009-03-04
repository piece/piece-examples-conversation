-- $Id: compositeprimarykey.ddl 331 2008-04-03 03:04:24Z iteman $

CREATE TABLE compositeprimarykey (
  album varchar (255) NOT NULL,
  artist varchar (255) NOT NULL,
  track int (11) NOT NULL,
  song varchar (255) NOT NULL,
  PRIMARY KEY (album, artist, track)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
