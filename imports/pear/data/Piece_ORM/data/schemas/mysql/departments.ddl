-- $Id: departments.ddl 331 2008-04-03 03:04:24Z iteman $

CREATE TABLE departments (
  id int (11) NOT NULL AUTO_INCREMENT,
  name varchar (255) NOT NULL,
  created_at datetime NOT NULL,
  updated_at timestamp,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
