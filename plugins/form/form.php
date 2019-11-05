<?php
namespace form;
/* ATTRIBUTTES ALLOWED
 * 
 * type: text, select, textarea
 * default: default value of field
 * value: value of the field
 * disabled: boolean
 * required: boolean
 * options: array of options
 * valideFunc: usa uma função externa para validar o campo (callable function)
 * transformFunc: usa uma função externa para transformar o valor antes de validar (callable function)
 * uploadDir: diretório de upload do arquivo do campo
 * uploadName: nome do arquivo de upload de campos do tipo arquivo (não incluir a extensão)
 * uploadFunction: função customizada de upload de arquivo
 */
class form{
	public $fields = Array(), $formErrors = 0, $fieldsRequired = true;
	/* options allowed */
	function addField($name, $atributes){
		$this->fields[$name] = $atributes;
		$value = (isset($this->fields[$name]['default']))? $this->fields[$name]['default'] : false;
		$value = (isset($this->fields[$name]['value']))? $this->fields[$name]['value'] : $value;
		if($value){
			$this->addFieldValue($name, $value);
		}
		return true;
	}
	function addFieldValue($field, $value, $autoValide = false){
	    if(is_array($field)){return false;}
        $value = (is_array($value))? implode('|', $value) : $value;
		if(isset($this->fields[$field])){
			$transform = (isset($this->fields[$field]['transformFunc']))? $this->fields[$field]['transformFunc'] : false;
            $transform = ($transform && !is_array($transform))? Array($transform) : $transform;
			if($transform){
			    foreach ($transform as $tfvalue) {
			        if(is_callable($tfvalue)){
			            $value = call_user_func($tfvalue, $value);
			        }
				}
			}
			$this->addFieldAttribute($field, 'value', $value);
			if($autoValide){
				$this->valideField($field);
			}
			return true;
		}
		return false;
	}
	function addFieldFile($field, $file, $autoValide = false){
		if(is_array($field) || is_array($file)){return false;}
		if(isset($this->fields[$field])){
			$this->addFieldAttribute($field, 'file', $file);
			if($autoValide){
				$this->valideField($field);
			}
			return true;
		}
		return false;
	}
	function addFieldAttribute($field, $attribute, $value){
		if(is_array($field) || is_array($attribute)){return false;}
		if(isset($this->fields[$field])){
			$this->fields[$field][$attribute] = $value;
			return true;
		}
		return false;
	}
	function arrayToFieldValues($array, $autoValide = false){
		if(!is_array($array)){return false;}
		foreach ($array as $key => $value) {
			$this->addFieldValue($key, $value, $autoValide);
		}
		return true;
	}
	function arrayToFieldFiles($array, $autoValide = false){
		if(!is_array($array)){return false;}
		foreach ($array as $key => $value){
			if(isset($value['name'])){
				$this->addFieldValue($key, $value['name'], $autoValide);
			}
			if(isset($value['tmp_name'])){
				$this->addFieldFile($key, $value['tmp_name'], $autoValide);
			}
		}
		return true;
	}
	function valideForm(){
		if($this->fields){
			foreach($this->fields as $key => $value) {
				$this->valideField($key);
			}
			return true;
		}
		return false;
	}
	function valideField($field){
		if(!isset($this->fields[$field])){return false;}
		$attributes = $this->fields[$field];
		$value = (isset($attributes['default']))? $attributes['default'] : null;
		$value = (isset($attributes['value']))? $attributes['value'] : $value;
		$type = (isset($attributes['type']))? mb_strtolower($attributes['type']) : 'text';
		$file = (isset($attributes['file']))? $attributes['file'] : null;
		$minlen = (isset($attributes['minlen']) && $attributes['minlen'])? intval($attributes['minlen']) : 0;
		$maxlen = (isset($attributes['maxlen']) && $attributes['maxlen'])? intval($attributes['maxlen']) : 200;
		$min = (isset($attributes['min']) && $attributes['min'])? intval($attributes['min']) : 0;
		$max = (isset($attributes['max']) && $attributes['max'])? intval($attributes['max']) : 99999999;
		$options = (isset($attributes['options']) && $attributes['options'])? $attributes['options'] : false;
		$valideFunc = (isset($attributes['valideFunc']) && $attributes['valideFunc'])? $attributes['valideFunc'] : false;
        $valideFunc = ($valideFunc && !is_array($valideFunc))? Array($valideFunc) : $valideFunc;
		$required = (isset($attributes['required']))? (bool)$attributes['required'] : $this->fieldsRequired;
		$valid = true;
		$errorAttr = null;
        $errorMessage = null;
		$this->formErrors --;
		// validar field
		if($options){// VALIDAR OPTIONS
			if(!isset($options[$value]) && (in_array($value, $options) && !is_numeric(array_search($value, $options)))){
				$valid = false;
				$errorAttr = 'options';
                $errorMessage = "A opção selecionada não é válida.";
			}
		}
		if($type == 'file'){// campo de arquivos
			if(!$file || !file_exists($file)){
				$valid = false;
				$errorAttr = 'file';
                $errorMessage = 'Arquivo não encontrado.';
			}
		}elseif($type == 'number' || $type == 'float'){// number, float
			if(is_numeric($value) && $value < $min){// VALIDAR MIN NUMBER
				$valid = false;
				$errorAttr = 'min';
                $errorMessage = 'Valor mínimo: '.$min.'.';
			}
			if(is_numeric($value) && $value > $max){// VALIDAR MAX NUMBER
				$valid = false;
				$errorAttr = 'max';
                $errorMessage = 'Valor máximo: '.$max.'.';
			}
		}else{// string (text, textarea)
			if(is_string($value) && $minlen && !isset($value[$minlen-1])){// VALIDAR MINLEN
				$valid = false;
				$errorAttr = 'minlen';
                $errorMessage = 'Requer no mínimo '.($minlen).' caracter(es).';
			}
			if(is_string($value) && isset($value[$maxlen+1])){// VALIDAR MAXLEN
				$valid = false;
				$errorAttr = 'maxlen';
                $errorMessage = 'Requer no máximo '.$maxlen.' caracter(es).';
			}
		}
		if($valideFunc){// VALIDAR COM FUNÇÃO
            foreach ($valideFunc as $vfkey => $vfvalue){
                if(is_callable($vfvalue)){
                    $vfReflection = new \ReflectionFunction($vfvalue);
                    $vfMaxParameters = $vfReflection->getNumberOfRequiredParameters();
                    $valideFuncErrorMessage = '';
                    $valideFuncParameters = Array();
                    if($vfMaxParameters > 0){
                        $valideFuncParameters[0] = $value;
                    }
                    if($vfMaxParameters > 1){
                        $valideFuncParameters[1] = &$valideFuncErrorMessage;
                    }
                    if(!call_user_func_array($vfvalue, $valideFuncParameters)){
                        $valid = false;
                        $errorAttr = 'valideFunc';
                        $errorMessage = $valideFuncErrorMessage;
                    }
                }
            }
		}
		if(!$required && (!$value || ($type == "file" && !$file))){
			$valid = true;
			$errorAttr = null;
            $errorMessage = null;
		}
		// set attributtes
		if($valid){
		    $this->fields[$field]['error'] = false;
		    $this->fields[$field]['error-attr'] = null;
            $this->fields[$field]['error-message'] = null;
			$this->formErrors ++;
		}else{
		    $this->fields[$field]['error'] = true;
			$this->fields[$field]['error-attr'] = $errorAttr;
            $this->fields[$field]['error-message'] = $errorMessage;
			$this->formErrors += 2;
		}
		return $valid;
	}
	function uploadFieldFile($field){
		if(isset($this->fields[$field]['file']) && file_exists($this->fields[$field]['file'])){
			$this->formErrors --;
			$customUploadFunction = (isset($this->fields[$field]['uploadFunc']))? $this->fields[$field]['uploadFunc'] : false;
			$nameArray = explode('.', $this->fields[$field]['value']);
			$fileExt = end($nameArray);
			$uploadName = (isset($this->fields[$field]['uploadName']))? $this->fields[$field]['uploadName'] : $field;
			$uploadName .= ($fileExt)? '.'.$fileExt : '';
			$fileDest = (isset($this->fields[$field]['uploadDir']))? $this->fields[$field]['uploadDir'].'/' : false;
			$fileDest .= $uploadName;
			$this->addFieldValue($field, $uploadName);
			// upar arquivo
			$fileReturn = false;
			if(is_callable($customUploadFunction)){
				$fileReturn = call_user_func($customUploadFunction, $this->fields[$field]['file'], $fileDest);
			}elseif($fileDest){
				$fileReturn = copy($this->fields[$field]['file'], $fileDest);
			}
			if(!$fileReturn){
				$this->formErrors += 2;
                $this->fields[$field]['error-message'] = 'Não foi possível realizar o upload do arquivo.';
				$this->fields[$field]['error-attr'] = 'file';
				$this->fields[$field]['error'] = 1;
			}else{
				$this->formErrors += 1;
			}
			return $fileReturn;
		}
		return false;
	}
	function uploadFiles(){
		if(!$this->fields){return false;}
		foreach ($this->fields as $key => $value){
			if(isset($value['type']) && $value['type'] == 'file'){
				$this->uploadFieldFile($key);
			}
		}
		return true;
	}
	function serializedForm($onlyValide = true, $keyAttribute = false){
		$form = Array();
		if($this->fields){
			foreach ($this->fields as $key => $attributes){
				if($keyAttribute){
					$key = (isset($attributes[$keyAttribute]))?$attributes[$keyAttribute]:false;
				}
				$value = (isset($attributes['default']))? $attributes['default'] : null;
				$value = (isset($attributes['value']))? $attributes['value'] : $value;
				$exist = (isset($attributes['default']) || isset($attributes['value']) || @$attributes['value'] == NULL)? true : false;
				$disabled = (isset($attributes['disabled']) && $attributes['disabled'])? true : false;
				if(!$exist || !$key){continue;}
				if(!$onlyValide || (isset($attributes['error']) && !$attributes['error'])){
					$form[$key] = $value;
				}
			}
		}
		return $form;
	}
	function serializedFormAttribute($attribute, $attributeToKey = false){
		$form = Array();
		if($this->fields){
			foreach ($this->fields as $key => $attributes){
				if($attributeToKey){
					$key = (isset($attributes[$attributeToKey]))?$attributes[$attributeToKey]:$key;
				}
				$value = (isset($attributes[$attribute]))?$attributes[$attribute]:null;
				$form[$key] = $value;
			}
		}
		return $form;
	}
}
