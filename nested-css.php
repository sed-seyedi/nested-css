<?php
class NestedCSS {
    ################ parse & include a file
    public function include($file_path,$cache=false){
        ob_start();
        include($file_path);
        $parsed_by_php = ob_get_clean();
        if($cache){
            $hash = hash("adler32",$parsed_by_php);
            $cache_loc = '/tmp/' . $hash . '.ncss';
            if(file_exists($cache_loc)){
                include($cache_loc);
                return;
            }
            $compiled_to_css = $this->parse($parsed_by_php);
            file_put_contents($cache_loc,$compiled_to_css);
            echo $compiled_to_css;
        }
        echo $this->parse($parsed_by_php);
    }
    ################ the main function that will complie our nested CSS
    public function parse($input){
        #### trim spaces from beg and end of lines
        $input = preg_replace('/(^\s*|\s*$)/m','',$input);
        #### remove comments
        $input = preg_replace('/\/\*(.*)\*\//Uis','', $input);
        #### remove new line from the end of lines that end with ","
        $input = preg_replace('/\,\n/m',',',$input); # add missing semicolons
        #### add missing semicolons
        $input = preg_replace('/([^\{\}\s\;])$/m','$1;',$input); # add missing semicolons
        #### make sure '}' is followed by new line
        $input = str_replace('}',"\n}\n",$input);
        #### make sure '{' is followed by a new line
        $input = str_replace('{',"{\n",$input);
        #### set defaults
        $ret = ''; # returned string (final result)
        $block = []; # used to keep track of each nested block (to save memory compiles each css block separately)
        $nest_level = 0; # keep track of nesting level
        $selector_name = ''; # will be used to capture last selector name
        #### lets loop over lines
        $lines = explode("\n",$input);
        foreach($lines as $line){
            $line = trim($line);
            if(substr($line,-1) == '{'){
                $selector_name = trim(rtrim($line,'{'));
                $nest_level++;
                $block[$nest_level.$selector_name]['name'] = $selector_name;
                $block[$nest_level.$selector_name]['nest_level'] = $nest_level;
            }elseif(substr($line,-1) == '}'){
                $nest_level--;
                continue;
            }
            if($nest_level == 0){
                $ret .= $this->block_to_css($block);
                $block = [];
            }
            if(substr($line,-1) == ';'){
                $uid = $nest_level.$selector_name;
                if(isset($block[$uid]['css']))
                    $block[$uid]['css'] .= $line;
                else
                    $block[$uid]['css'] = $line;
            }
        }
        return $ret;
    }
    ################ helper functions
    private function block_to_css($block){
        $css = '';
        if(!is_array($block)){
            return $css;
        }
        foreach($block as $selector){
            if(!isset($selector['css']))
                continue;
            $parents = $this->parent_selectors($block,$selector['nest_level']);
            $css .=  $parents . ' ' . $selector['name'] . '{' . $selector['css'] . '}';
        }
        return $css;
    }
    private function parent_selectors($block, $nest_level){
        $all_parents = '';
        foreach($block as $selector){
            if($selector['nest_level'] < $nest_level)
                $all_parents .= $selector['name'];
        }
        return $all_parents;
    }
}
