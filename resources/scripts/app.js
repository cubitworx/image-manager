import 'jquery';
import 'bootstrap';
import 'bootstrap-slider';
import { isEmpty, uniq } from 'lodash';
import Vue from 'vue';
import Toasted from 'vue-toasted';

import './bootstrap';

Vue.use(Toasted);

new Vue({
	el: '#app',
	computed: {
		availableKeywords() {
			return this.selectedImages.length
				? this.editor.available_keywords.filter((keyword) => {
					const term = this.editor.new_keyword.toLowerCase();
					return !this.editor.keywords.includes(keyword) && (!term || (keyword.toLowerCase().indexOf(term) !== -1));
				})
				: this.editor.available_keywords;
		},
		filteredFiles() {
			return this.files.filter((file) => {
				const term = this.filter.toLowerCase();
				return !term
					|| (file.filename.toLowerCase().indexOf(term) !== -1)
					|| !file.iptc
					|| ['2#080', '2#120', '2#085', '2#025'].reduce((result, item) => {
						return result
							? result
							: file.iptc[item]
								? file.iptc[item].join('|').toLowerCase().indexOf(term) !== -1
								: false;
					}, false);
			});
		},
	},
	data: {
		files: [],
		filter: '',
		editor: {
			available_keywords: [],
			author: '',
			description: '',
			keywords: [],
			new_keyword: '',
			state: 'untouched',
			title: '',
		},
		grid: {
			size: 2,
		},
		placeholders: {
			author: '',
			description: '',
			title: '',
		},
		selectedImages: [],
	},
	methods: {
		addKeyword(keyword) {
			if (!this.selectedImages.length)
				return this.$toasted.error('Select an image first', this.toastedOptions());
			if (!this.editor.keywords.includes(keyword)) {
				this.editor.keywords.push(keyword);
				this.addImagesArr('keywords', keyword);
				this.editor.state = 'modified';
			} else {
				this.$toasted.error('Image already contains keyword', this.toastedOptions());
			}
		},
		addImagesArr(key, item) {
			const code = {
				keywords: '2#025',
			}[key];

			if (code) {
				this.selectedImages.forEach((image) => {
					image.iptc[code] = image.iptc[code] || [];
					if (!image.iptc[code].includes(item))
						image.iptc[code].push(item);
				});
			}
		},
		addNewKeyword(event) {
			event.preventDefault();
			if (this.editor.new_keyword) {
				if (!this.editor.available_keywords.includes(this.editor.new_keyword))
					this.editor.available_keywords.push(this.editor.new_keyword);
				this.addKeyword(this.editor.new_keyword);
				this.editor.new_keyword = '';
				this.editor.state = 'modified';
			}
		},
		deselectAllImages() {
			this.selectedImages = [];
			this.files.forEach((item) => item.__meta__.selected = false);
			this.updateEditor();
		},
		deselectImage(file, modifier) {
			switch (modifier) {
				case 1:
					this.selectImage(file, modifier);
					break;
				case 2:
					file.__meta__.selected = false;
					this.selectedImages.splice(this.selectedImages.findIndex((item) => item.uid === file.uid), 1);
					break;
				case 0:
				default:
					this.deselectAllImages();
					file.__meta__.selected = true;
					this.selectedImages.push(file);
					break;
			}
			this.updateEditor();
		},
		init() {
			$('#grid-size').slider({
				tooltip_position: 'bottom',
				value: this.grid.size,
			}).on('change', (event) => {
				this.grid.size = event.value.newValue;
			});

			axios.get('/metadata')
				.then((response) => {
					this.files = response.data.files.map((file) => {
						Object.assign(file, {
							__meta__: { selected: false },
							exif: !isEmpty(file.exif) ? file.exif : {},
							iptc: !isEmpty(file.iptc) ? file.iptc : {},
						});
						return file;
					});
					this.editor.available_keywords = uniq(response.data.files.flatMap((file) => file.iptc['2#025'] || []));
				}).catch((err) => {
					console.error(err);
					this.$toasted.error('Error reading files', this.toastedOptions());
				});
		},
		isImageSelected(file) {
			return file.__meta__.selected === true;
		},
		onImageClick(event, file) {
			const modifier = event.shiftKey ? 1 : event.ctrlKey ? 2 : 0;
			if (this.isImageSelected(file))
				this.deselectImage(file, modifier);
			else
				this.selectImage(file, modifier);
		},
		removeImagesArr(key, item) {
			const code = {
				keywords: '2#025',
			}[key];

			if (code) {
				this.selectedImages.forEach((image) => {
					image.iptc[code] = image.iptc[code] || [];
					if (image.iptc[code].includes(item))
						image.iptc[code].splice(image.iptc[code].findIndex((keyword) => item === keyword), 1);
				});
			}
		},
		removeKeyword(keyword) {
			this.editor.keywords.splice(this.editor.keywords.findIndex((item) => item === keyword), 1);
			this.removeImagesArr('keywords', keyword);
			this.editor.state = 'modified';
		},
		save() {
			const files = this.files.map((file) => ({
				exif: file.exif,
				filename: file.filename,
				group_uid: file.group_uid,
				iptc: file.iptc,
				uid: file.uid,
			}));

			axios.post('/metadata', {files})
				.then(() => {
					this.editor.state = 'saved';
					this.$toasted.success('Changes saved', this.toastedOptions());
				}).catch((err) => {
					console.error(err);
					this.$toasted.error('Error saving changes', this.toastedOptions());
				});
		},
		selectAllImages() {
			if (this.selectedImages.length) {
				this.deselectAllImages();
				this.files.forEach((file) => file.__meta__.selected = false);
			} else {
				this.filteredFiles.forEach((file) => file.__meta__.selected = true);
				this.selectedImages = this.filteredFiles;
				this.updateEditor();
			}
		},
		selectImage(file, modifier) {
			switch (modifier) {
				case 1:
					if (this.selectedImages.length) {
						let currentIndex = this.filteredFiles.findIndex((item) => item.uid === this.selectedImages[0].uid || 0);
						const endIndex = this.filteredFiles.findIndex((item) => item.uid === file.uid);
						if (currentIndex !== endIndex) {
							const direction = currentIndex < endIndex;
							this.deselectAllImages();
							do {
								let filteredFile = this.filteredFiles[currentIndex];
								filteredFile.__meta__.selected = true;
								this.selectedImages.push(filteredFile);
							} while (direction ? (currentIndex++ < endIndex) : (currentIndex-- > endIndex));
						}
					}
					break;
				case 2:
					file.__meta__.selected = true;
					this.selectedImages.push(file);
					break;
				case 0:
				default:
					this.deselectAllImages();
					file.__meta__.selected = true;
					this.selectedImages.push(file);
					break;
			}
			this.updateEditor();
		},
		toastedOptions() {
			return {
				fullWidth: true,
				duration: 2000,
			};
		},
		updateEditor() {
			this.updateEditorStr('author', '2#080');
			this.updateEditorStr('description', '2#120');
			this.updateEditorStr('title', '2#085');
			this.updateEditorArr('keywords', '2#025');
		},
		updateEditorArr(field, key) {
			this.editor[field] = [];
			if (this.selectedImages.length) {
				let current = [], tmp;
				this.editor[field] = this.selectedImages.reduce((result, file) => {
					current = file.iptc && file.iptc[key] || [];
					if (result === null)
						return current;
					if (!result.length)
						return result;
					tmp = result.filter((value) => current.includes(value));
					return tmp;
				}, null);
			}
		},
		updateEditorStr(field, key) {
			this.editor[field] = '';
			this.placeholders[field] = '';
			if (this.selectedImages.length) {
				let current = '';
				this.editor[field] = this.selectedImages.reduce((result, file) => {
					current = file.iptc && file.iptc[key] || [];
					if (result === null)
						return current[0];
					if (current[0] === result)
						return result;
					this.placeholders[field] = '[Multiple values]';
					return '';
				}, null);
			}
		},
		updateImagesStr(key) {
			const code = {
				author: '2#080',
				description: '2#120',
				title: '2#085',
			}[key];

			if (code) {
				this.selectedImages.forEach((file) => file.iptc[code] = [this.editor[key]]);
				this.editor.state = 'modified';
			}
		},
	},
	mounted() {
    if (localStorage.gridSize)
      this.grid.size = localStorage.gridSize;
		this.init();
	},
  watch: {
    'grid.size': (gridSize) => {
      localStorage.gridSize = gridSize;
    }
  },
});
