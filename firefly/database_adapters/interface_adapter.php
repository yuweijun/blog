<?php
interface DatabaseAdapter {
    
	public function execute($sql);
	public function fetch_rows($sql);
	public function create($sql);
	public function update($sql);
	public function delete($sql);
	public function empty_insert_statement($table_name);
	public function quote_column_name($name);
	public function quote_table_name($name);
	public function columns($table_name);
	public function transaction_start();
	public function transaction_commit();
	public function transaction_rollback();

}
?>
