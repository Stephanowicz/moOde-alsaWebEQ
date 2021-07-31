<!DOCTYPE html> 
<html>
    <head>
        
        <title>WebMixer</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="public/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="public/css/jquery-ui.css"/>
        <link rel="stylesheet" href="public/css/app.css"/>

    </head>
    <body> 
        <script src="public/js/jquery.min.js"></script>
        <script src="public/js/jquery-ui.min.js"></script>
        <script src="public/js/jsviews.min.js"></script>
        <script src="public/js/bootstrap.min.js"></script>

        <div id="mixer" data-device="alsaequal">
    
            <div class="render_controls"></div>
            
        </div>
        <div style="font: 1rem 'Fira Sans', sans-serif;"><input type="checkbox" id="linkchannels" name="linkchannels" style="margin: .4rem;"><label for="linkchannels">link L/R</label></div>
        <div style="font: 1rem 'Fira Sans', sans-serif;"><input type="button" id="reset" name="reset" style="margin: .4rem;" value="reset"></div>
        <div style="font: 1rem 'Fira Sans', sans-serif;"><select id="outputs" name="outputs"></select></div>
        <script type="text/html" id="tpl_control">
                    
                {{for controls}}
                    <div class="col-xs-1" style="width:45px; padding: 0; margin: 0; margin-left: 5px;">
                        <div class="panel panel-default" >
                            <div class="panel-heading" style="text-align: center; padding-left:2px; padding-right: 2px; white-space: nowrap; overflow: hidden;">
                                <small>{{:name.slice(4)}}</small>
                            </div>
                            {{if volume}}
                                <div class="panel-body">
                                    <div class="row control">
                                        {{for volume.channels}}
                                            <div class="col-xs-6 " style="padding: 0; margin: 0; ">
                                                <div class="slider" title="{{:name}}" data-id="{{:id}}" data-channel="{{:channel}}" data-min="{{:min}}" data-max="{{:max}}" data-step="{{:step}}" data-value="{{:current}}" style="margin: 10px;"></div>
                                                <div class="control-level" style="font-size: 80%; text-align: center;"></div>                                            
                                            </div>
                                        {{/for}}
                                    </div>
                                </div>
                            {{/if}}
                            <!--<a class="bt-customize-control" data-id="{{:id}}" data-name="{{:name}}" style="cursor: pointer;"><span class="glyphicon glyphicon-cog"></span></a>-->
                        </div>    
                    </div>
                {{/for}} 
        </script>

        <script>
        $(function(){
          
            var page = $("#mixer"),
                device = page.data('device'),
                controls = $('.render_controls',page),
                template = $.templates("#tpl_control");
          
            function setControl(data, ui){
                
                var values = []; 

                $('.slider[data-id='+data.id+']', controls).each(function(){
                    values.push( $(this).slider("value") );
                });

	            $.post('alsadevice.php', {command:'set',device:device,id:data.id, channel:data.channel, value: values.join(",")}, function(response){
//	                console.log("set: ", response);   
	            }, 'json');
            }
    
            function resetControl(){
	            $.post('alsadevice.php', {command:'reset'}, function(response){
//	                console.log("reset: ", response);   
					update(response);
	            }, 'json');
                
            }
            
            function bind(){                
                $(".slider", controls).each(function(){
                    var slide = $(this),
                        level = slide.parents(".control").find(".control-level"),
                        data = slide.data(),
                        options = $.extend({
                            orientation: "vertical",
                            create: function() {
                                level[options.channel].textContent=options.value;
                            },
                            slide: function (event, ui) {
                                
                               // level.text( ui.value); //  + "dB" );
                                level[options.channel].textContent=ui.value;
                                setControl(options, ui);
                                
                            }, 
                            stop: function (event, ui) {
                                level[options.channel].textContent=ui.value;
                                setControl(options, ui);
                            },

                        }, data);

                    slide.slider(options);

                });

                $(':input', controls).on({

                    change:function(){

                        var post = {
							command: 'set',
                            id: $(this).data('id'),
                            device:device,
                            channel: $(this).data('channel'),
                            value: null
                        };

                        if($(this).is(":checkbox")){
                            post.value = $(this).prop('checked')?"on":"off";
                        } else {
                            post.value = $(this).val();
                        }

                        mixer.set(post, function(response){

//                            console.log("Response", response);
                            
                        });
                    }

                });   

                $('.customize-modal',page).on({
                    
                    'init':function(){
                        $(this).modal('hide');
                    },
                    'show':function(e, data){
                        
                        
                        
                        $(this).modal('show');
                        
                    },
                    'hide':function(){
                        $(this).modal('close');
                    },
                    
                }).trigger('init');
                
                $('.bt-customize-control',controls).on({
                    click:function(){
                        
                        var bt = $(this),
                            id = bt.data('id');
                    
                        $('.customize-modal',page).trigger('show', {
                            'id':id
                        });
                        
                    }
                });
        		$('#reset').click(resetControl);
            }

			function init(){
	            $.post('alsadevice.php', {'command': 'get'}, function(result){
                    $(result["outputs"][0]).each(function(x,val){
                        $('#outputs')[0].options.add(new Option(val, x));
                    });
					var sel = result.outputs[1].enabled;
                    $('#outputs')[0].options[sel].selected = true;
                    $('#outputs').change(setOutput)
					controls.empty();
	                controls.append( template.render(result) );
	                bind();
	            }, 'json');

			}

			function update(result){
 //               console.log("update: ",result["output"]["controls"]);
                $(result["output"]["controls"]).each(function(x){
 //                   console.log("ID: ", $(this)[0].id);
 //                   console.log("Volume: ", $(this)[0].volume.values[0]);
                    
                    $($(".slider", controls)[x*2]).slider( "value", $(this)[0].volume.values[0] );                    
                    $($(".slider", controls)[x*2+1]).slider( "value", $(this)[0].volume.values[1] );                    
                    $($(".slider", controls)[x*2]).parents(".control").find(".control-level")[0].textContent=$(this)[0].volume.values[0];
                    $($(".slider", controls)[x*2]).parents(".control").find(".control-level")[1].textContent=$(this)[0].volume.values[1];
                });
			}
            
            function setOutput()
            {
//                console.log("Selected Index:", $('#outputs')[0].options.selectedIndex);
                $.post('alsadevice.php', {command:'setOutput',output:$('#outputs')[0].options.selectedIndex}, function(response){
	                console.log("setoutput: ", response);   
	            }, 'json');

            }
            
			init(); 
         
        });  
        </script>
        
    </body>
</html>