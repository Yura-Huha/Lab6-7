<?php
abstract class BaseList{
		protected $dataArray;
		protected $index;
		protected $conn;
		public function __construct($conn){
			$this->dataArray=[];
			$this->index=0;
			$this->conn=$conn;
		}
		public function convertToJSON(){
			header("Content-type: application/json");
			$jsonArray=[];
			for ($i=0; $i<count($this->dataArray);$i++){
				array_push($jsonArray,$this->dataArray[$i]->getAsJSONObject());
			}
			return json_encode($jsonArray,JSON_UNESCAPED_UNICODE);
		}
		public function getTable(){
			$tableContent='';
			for ($i=0; $i<count($this->dataArray);$i++){
				$tableContent.=$this->dataArray[$i]->getDataAsTableRow();
			}
			return $tableContent;
		}
		public function showAll(){
			for ($i=0; $i<count($this->dataArray);$i++){
				echo $this->dataArray[$i]->displayInfo();
			}
		}
		public abstract function importFromFile($fileName);
		public function delete($id){
			for ($i=0; $i<count($this->dataArray);$i++){
				if ($this->dataArray[$i]->getId()==$id){
					array_splice($this->dataArray,$i,1);
					break;
				}
			}
		}
		public function exportToFile($fileName){
			if (($handle = fopen($fileName, "w")) !== FALSE) {
				for ($i=0; $i<count($this->dataArray);$i++){
					fwrite($handle,$this->dataArray[$i]->getDataAsCSVRow());
				}
				fclose($handle);
			}
			
		}
	}