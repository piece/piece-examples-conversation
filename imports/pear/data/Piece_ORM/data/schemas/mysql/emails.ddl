-- $Id: emails.ddl 331 2008-04-03 03:04:24Z iteman $

CREATE TABLE emails (
  emails_id int (11) NOT NULL AUTO_INCREMENT,
  email varchar (255) NOT NULL,
  created_at datetime NOT NULL,
  updated_at timestamp,
  PRIMARY KEY (emails_id),
  UNIQUE (email)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
