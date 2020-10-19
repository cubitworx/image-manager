<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta name="robots" content="none">

	@include('snippets.head')

	<title>{{ $title ?? config('app.name') }}</title>

	@stack('before-styles')
	@include('bundles.app-head')
	@stack('after-styles')

	@yield('head')

</head>
<body class="template-master-app {{ $theme ?? '' }} {{ $class ?? '' }}" id="{{ $id ?? 'body' }}">
	@yield('body-start')

	@yield('before-contents')
	<div class="page-contents" id="app">
		@yield('content')
	</div>
	@yield('after-contents')

	@stack('before-scripts')
	@include('bundles.app-body')
	@stack('after-scripts')

	@yield('body-end')
</body>
</html>
