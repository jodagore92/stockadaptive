<?php

class funciones{

    function ImprimirTablaHtml($tabla){    
        if(count($tabla)>0){
            echo "<table border='1'>";
            $cabecera = reset($tabla);
            echo "<tr>";
            foreach($cabecera as $key => $value){
                echo "<th>$key</th>";
            }
            echo "</tr>";
            foreach($tabla as $res){
                echo "<tr>";
                foreach($res as $item){
                    echo "<td>$item</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
}
