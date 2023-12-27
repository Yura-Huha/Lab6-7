<?php
require_once('BaseList.php');
	class EbookList extends BaseList{
		public function add($brand, $model, $category, $properties){
			$id=++$this->index;
			$ne=new Ebook($id,$brand, $model, $category, $properties);
			array_push($this->dataArray,$ne);
			return $id;
		}
        public function getAllFromDatabase(){
            $sql = "SELECT `ebook`.*,`category`.name catname FROM `ebook` 
            INNER JOIN `category` ON `ebook`.category_id=`category`.id  WHERE 1";
            $result = $this->conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $nc=new Ebook($row['id'],$row['brand'],$row['model'],$row['catname'],$this->getEbookPropertiesById($row['id']));
                    array_push($this->dataArray,$nc);
                }
            } else {
            echo "0 results";
            }
        }

		public function getAllFromDatabaseBySearchCriteria($search){
			$sql = "SELECT `ebook`.*,`category`.name catname FROM `ebook` 
            INNER JOIN `category` ON `ebook`.category_id=`category`.id  WHERE
			LOWER(`ebook`.brand) LIKE LOWER('%".$search."%')
			OR LOWER(model) LIKE LOWER('%".$search."%')
			OR LOWER(`category`.name) LIKE LOWER('%".$search."%')";
			$result = $this->conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $nc=new Ebook($row['id'],$row['brand'],$row['model'],$row['catname'],$this->getEbookPropertiesById($row['id']));
                    array_push($this->dataArray,$nc);
                }
            } else {
            echo "0 results";
            }
		}
		public function getFromDatabaseById($id){
            $sql = "SELECT * FROM `ebook` WHERE id=".$id;
            $result = $this->conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    return $row;
                }
            } else {
            echo "0 results";
            }
        }
		public function updateDatabaseById($id,$brand,$model,$category){
            $stmt = $this->conn->prepare("UPDATE `ebook` SET `brand`=?,`model`=?,`category_id`=? WHERE `id`=?;");
            $stmt->bind_param("ssss", $brand,$model,$category,$id);
            $stmt->execute();
        }
		public function deleteFromDatabase($id){
			$stmt = $this->conn->prepare("DELETE FROM `ebook` WHERE id=?;");
            $stmt->bind_param("s", $id);
            $stmt->execute();
			$stmt = $this->conn->prepare("DELETE FROM `ebook_property` WHERE ebook_id=?;");
            $stmt->bind_param("s", $id);
            $stmt->execute();
		}
		public function insertIntoDatabase($brand, $model, $category){
            $stmt = $this->conn->prepare("INSERT INTO `ebook` VALUES(DEFAULT,?,?,?);");
            $stmt->bind_param("sss", $brand, $model, $category);
            $stmt->execute();
            $last_id = $this->conn->insert_id;
			return $last_id;
        }
		public function addEbookProperty($ebookId, $propertyId, $value){
			$stmt = $this->conn->prepare("INSERT INTO `ebook_property` VALUES(DEFAULT,?,?,?);");
            $stmt->bind_param("sss", $ebookId, $propertyId, $value);
            $stmt->execute();
		}
		public function refreshEbookProperty($ebookId, $propertyId, $value){
			$stmtDelete = $this->conn->prepare("DELETE FROM `ebook_property` WHERE `property_id`=? AND `ebook_id`=? ");
            $stmtDelete->bind_param("ss", $propertyId,$ebookId);
            $stmtDelete->execute();
			$stmtAdd = $this->conn->prepare("INSERT INTO `ebook_property` VALUES(DEFAULT,?,?,?);");
            $stmtAdd->bind_param("sss", $ebookId, $propertyId, $value);
            $stmtAdd->execute();
		}
		
        public function getEbookPropertiesById($id){
            $sql = "SELECT `ebook_property`.*, `property`.name, `property`.`units` 
            FROM ebook_property INNER JOIN `property` 
            ON `property`.`id`=`ebook_property`.`property_id` 
            WHERE `ebook_property`.`ebook_id`=".$id;
            $result = $this->conn->query($sql);
            $propsArray=[];
            if ($result->num_rows > 0) {
                // output data of each row
                    while($row = $result->fetch_assoc()) {
                        array_push($propsArray,$row);
                    }
                } else {
                echo "0 results";
                }
            return $propsArray;
        }
		public function getDataAsXML(){
			header("Content-type: text/xml");
			$result='<?xml version="1.0" encoding="UTF-8"?>
			<ebooks>';
			for ($i=0; $i<count($this->dataArray);$i++){
				$result.=$this->dataArray[$i]->getDataAsXML();
			}
			$result.='</ebooks>';
			return $result;
		}
		public function importFromFile($fileName){
			$row = 1;
			if (($handle = fopen($fileName, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				eval('$propsArray='.$data[4].';');
				$this->add($data[0],$data[1],$data[2],$propsArray);
				$row++;	
			}
			fclose($handle);
			}
		}
        
		public function edit($id,$brand, $model, $category, $properties){
			for ($i=0; $i<count($this->dataArray);$i++){
				if ($this->dataArray[$i]->getId()==$id){
					$this->dataArray[$i]->edit($brand, $model, $category, $properties);
					break;
				}
			}
		}
	}
	class Ebook{
		private $id;
		private $brand;
		private $model;
		private $category;
		private $properties;
		public function __construct($id, $brand, $model, $category, $properties){
			$this->id=$id;
			$this->brand=$brand;	
			$this->model=$model;
			$this->category=$category;
			$this->properties=$properties;	
		}
		public function getId(){
			return $this->id;
		}
		public function getDataAsCSVRow(){
			return '"'.addslashes($this->brand).'","'.addslashes($this->model).'","'.addslashes($this->category).'","'.$this->getPropertiesForCSV().'"'."\n";
		}
		public function getDataAsXML(){
			return "
				<ebook>
					<brand>".$this->brand."</brand>
					<model>".$this->model."</model>
					<category>".$this->category."</category>
					<properties>".$this->getPropertiesAsXML()."</properties>
				</ebook>
			";
		}
		public function getDataAsTableRow(){
			return "
				<tr>
					<td>".$this->brand."</td>
					<td>".$this->model."</td>
					<td>".$this->category."</td>
					<td>".$this->displayProperties()."</td>
					<td>
					<a href='ebook.php?id=".$this->id."'>Редагувати</a>
					<form method='POST'>
						<input type='hidden' name='action' value='delete'/>
						<input type='hidden' name='id' value='".$this->id."'/>
						<button type='submit'>Видалити</button>	
					</form></td>
				</tr>
			";
		}
		public function edit($brand, $model, $category, $properties){
			$this->brand=$brand;	
			$this->model=$model;
			$this->category=$category;
			$this->properties=$properties;	
		}
		public function getAsJSONObject(){
			return get_object_vars($this);
		}
		private function getPropertiesForCSV(){
			$result="[";
			foreach($this->properties as $key => $value) {
				$result.=  "'".addslashes($key) . "' => '" . addslashes($value)."'";
				$result.=",";
			}
			$result=substr_replace($result ,"", -1);
			$result.="]";
			return $result;
		}
		private function displayProperties(){
			$result='';
			foreach($this->properties as $property) {
				$result.=  $property['name'] . ": " . $property['value']." (". $property['units'].")";
			  	$result.=  "<br>";
			}
			return $result;
		}
		private function getPropertiesAsXML(){
			$result='';
			foreach($this->properties as $key => $value) {
			  	$result.="<property><key>".$key."</key><value>".$value."</value></property>";
			}
			return $result;
		}
		public function displayInfo(){
			return $this->id.". <b>".$this->brand." ".$this->model."</b></br>
			Категорія: ".$this->category."<br>". $this->displayProperties();
		}
		public function __destruct(){
			echo "";	
		}
	}
?>