$(document).ready(function(){
    
       var html = '<tr><td><p>Laptop</p></td><td><input type="text" class="form-control"></td><td><textarea rows="2" cols="2" class="form-control"></textarea></td><td><div class="qty"><button class="qtyminus" aria-hidden="true">&minus;</button><input type="number" name="qty" id="qty" min="1" max="10" step="1" value="1"><button class="qtyplus" aria-hidden="true">&plus;</button></div></td><td><input type="number" class="form-control"></td><td>656625</td><td><button class="btn btn-danger remove"><i class="fa fa-times" aria-hidden="true"></i></button></td></tr>'; 
    	$("#addProduct").click(function(){
    		$('tbody').append(html);
    });
    
    $(document).on('click','.remove',function(){
        $(this).parents('tr').remove();
    });
});

