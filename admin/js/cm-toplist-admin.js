( function() {
  var vm = new Vue({
		el: '#app',
		template: `
		<div>
		<table class="form-table" v-if="cm_toplist_data && cm_toplist_data.length">
		<tr>
			<th>ID</th>
			<th>Casino</th>
			<th>Rating</th>
			<th>Add / Delete</th>
		</tr>
      <tr v-for="(cm_toplist, index) in cm_toplist_data" :key="index">
        <td>
          {{ cm_toplist.id }}
        </td>
        <td>
          {{ cm_toplist.name }}
        </td>
        <td>Rating: {{ cm_toplist.rating }}</td>
        <td><button class="button-primary" @click="delete_item(index, cm_toplist.id )">Delete</button></td>
      </tr>
      <tr>
        <td>
          <!-- ID: disabled -->
        </td>
        <td>
          <!-- {{ user enter: name }} -->
          <input type="text" v-model="casino_name">
        </td>
        <td>
          <!-- {{ user enter: rating }} -->
          <input type="text" v-model="casino_rating">
        </td>
        <td><button class="button-primary" @click="add_new">Add new</button></td>
      </tr>    
		</table>
		<h3>Debugging</h3>
		<ul class="form-table" v-if="errors && errors.length">

			<li v-for="(error, index) in errors" :key="index">
				{{ error.code }}
			</li>
		</ul>
		</div>
		`,
		data() {
			return {
				cm_toplist_data: [],
				errors: [],
				casino_name: '',
				casino_rating: '',
			};
		},
		methods: {
			delete_item: function(index, delete_id) {
				axios
				.delete(`http://localhost/jtron-plugin-dev/wp-json/cm-toplist/v1/route/?brand_id=${delete_id}`)
				.then(response => {
					console.log(response);
					this.cm_toplist_data.splice(index, index);
				})
				.catch(e => {
					console.log(e);
					this.errors.push(e);
				});
			},
			add_new: function() {
				axios
				.post(`http://localhost/jtron-plugin-dev/wp-json/cm-toplist/v1/route`, {
					brand_name: this.casino_name,
					brand_rating: this.casino_rating,
				})
				.then(response => {
					console.log(response);

					this.cm_toplist_data.push({
						id: response.data.brand_id,
						name: response.data.brand_name,
						rating: response.data.brand_rating
					});
				})
				.catch(e => {
					console.log(e);

					this.errors.push(e);
				});
			}
		},
    mounted: function(){
			console.log("cm-toplist-vue-admin activated!");
			axios
      .get(`http://localhost/jtron-plugin-dev/wp-json/cm-toplist/v1/route`)
      .then(response => {
        this.cm_toplist_data = response.data;
      })
      .catch(e => {
        this.errors.push(e);
      });
    }
  });
})();