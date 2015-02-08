<?php
    class Model_Keg extends RedBean_SimpleModel {
            public function update() {
                if ( !is_numeric( $this->bean->size ) || !is_numeric( $this->bean->pour_size )){
					throw new Exception( 'Keg and pour sizes must be numbers' );	
				}
            }
    }
?>