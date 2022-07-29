@if(count($errors))
<div class="alert alert-warning" role="alert">
     @foreach ($errors->all() as $error)
     
       <strong>{{$error}}!<br></strong>
      
     @endforeach
    </div>   
@endif