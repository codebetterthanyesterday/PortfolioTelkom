@extends('layout.layout')

@section('title', 'Coming Soon')

@section('content')
<style>
    .coming-wrapper{min-height:60vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem;}
    .coming-wrapper h1{font-size:3rem;font-weight:700;letter-spacing:.05em;margin:0 0 .75rem;}
    .coming-wrapper p{max-width:480px;font-size:1.05rem;color:#555;margin:0;}
</style>
<div class="coming-wrapper">
    <h1>Coming Soon</h1>
    <p>We are crafting this page. Please check back later.</p>
</div>
@endsection