<?php
class foo {
    static public function test() {
        // php >= 5.3.0
        var_dump(get_called_class());
    }
}

class bar extends foo {
}

foo::test();
bar::test();

?>
