CREATE TABLE orders (
  id int (11) NOT NULL AUTO_INCREMENT,
  main int (11) NOT NULL,
  side int (11) NOT NULL,
  created_at datetime NOT NULL,
  updated_at timestamp,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*
 * Local Variables:
 * mode: sql
 * coding: iso-8859-1
 * tab-width: 2
 * indent-tabs-mode: nil
 * End:
 */
