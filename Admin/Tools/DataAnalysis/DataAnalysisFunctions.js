    function get_script_loader() {
        var loaded_scripts = [];
        
        return function(script_src) {
            if (loaded_scripts.indexOf(script_src) === -1) {
                var script = document.createElement('script');
                script.src = script_src;
                document.head.appendChild(script);
                
                loaded_scripts.push(script_src);
            }
        }
    }
    
    var load_script = get_script_loader();