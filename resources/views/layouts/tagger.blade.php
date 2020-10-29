@extends('templates.master-app')

@section('content')

	<div class="sidepanel container-fluid">
		<div class="row">

			<div class="form-group col-12">
				<label>Title</label>
				<input type="text" v-model.trim="editor.title" v-on:input="updateImagesStr('title')" v-bind:disabled="!selectedImages.length" v-bind:placeholder="placeholders.title" class="form-control" />
			</div>

			<div class="form-group col-12">
				<label>Description</label>
				<textarea v-model.trim="editor.description" rows="4" v-on:input="updateImagesStr('description')" v-bind:disabled="!selectedImages.length" v-bind:placeholder="placeholders.description" class="form-control"></textarea>
			</div>

			<div class="keywords keywords-selected form-group col-12">
				<label>Selected Keywords</label>
				<ul class="list-group">
					<li v-for="keyword in editor.keywords" v-on:click="removeKeyword(keyword)" class="list-group-item d-flex justify-content-between align-items-center p-2">
						@{{ keyword }}
						<i class="fas fa-minus-circle d-none"></i>
					</li>
				</ul>
			</div>

			<div class="keywords keywords-available form-group col-12">
				<label>Available Keywords</label>
				<form v-on:submit="addNewKeyword" class="input-group mb-1">
					<input type="text" v-model.trim="editor.new_keyword" class="form-control" placeholder="New keyword" aria-label="New keyword">
					<div class="input-group-append">
						<button type="submit" class="btn btn-light input-group-text">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</form>
				<ul class="list-group">
					<li v-for="keyword in availableKeywords" v-on:click="addKeyword(keyword)" class="list-group-item d-flex justify-content-between align-items-center p-2">
						@{{ keyword }}
						<i class="fas fa-plus-circle d-none"></i>
					</li>
				</ul>
			</div>

		</div>
	</div>

	<div class="contents">

		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarMain">
				<form class="form-inline my-2 my-lg-0">
					<input type="search" v-model.trim="filter" class="search form-control mr-sm-1" placeholder="Search" aria-label="Search">
					<button type="button" v-on:click="selectAllImages" class="btn btn-light mr-sm-1">
						<i class="fas fa-check-double"></i>
					</button>
					<button type="button" v-on:click="save" :class="editor.state" class="btn btn-light btn-save mr-sm-3">
						<i class="fas fa-save"></i>
					</button>
					<input type="text" v-model="grid.size" data-slider-min="1" data-slider-max="5" data-slider-step="1" data-slider-ticks="[1, 2, 3, 4, 5]" data-slider-id="grid-size" id="grid-size" />
				</form>
				<a href="/" class="navbar-brand ml-auto mr-0">
					<img src="/images/logo.svg" width="30" height="30" alt="" loading="lazy">
				</a>
			</div>
		</nav>

		<div class="image-grid p-1">
			<div v-for="file in filteredFiles" v-on:click="onImageClick($event, file)" :class="['x' + grid.size, {active: selectedImages && isImageSelected(file)}]" class="image-box p-1 d-inline-block">
				<img :src="`/storage/images/thumbnails/${file.group_uid}/${file.filename}`" class="img-fluid img-thumbnail" />
			</div>
		</div>

		</div>
	</div>

@endsection
