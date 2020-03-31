( function() {
  var vm = new Vue({
		el: '#app',
		template: `
		<div>
		<table class="form-table">
		<thead>
		<tr>
			<th>ID</th>
			<th>Casino</th>
			<th>Rating</th>
			<th>Add / Delete</th>
		</tr>
		</thead>
		<tbody v-if="cm_toplist_data && cm_toplist_data.length">
      <tr v-for="(cm_toplist, index) in cm_toplist_data" :key="index">
        <td>
          {{ cm_toplist.id }}
        </td>
        <td>
          {{ cm_toplist.name }}
        </td>
        <td>Rating: {{ cm_toplist.rating }}</td>
        <td><button class="button-primary" @click="delete_item(index, cm_toplist.id, cm_toplist.name )">Delete</button></td>
			</tr>
			</tbody>
			<tfoot>
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
			</tfoot>  
		</table>
		<h3>Debugging API requests/responses</h3>
		<ul class="form-table" v-if="msg && msg.length">

			<li v-for="(m, index) in msg" :key="index">
				{{ m }}
			</li>
		</ul>
		</div>
		`,
		data() {
			return {
				cm_toplist_data: [],
				msg: [],
				casino_name: '',
				casino_rating: '',
			};
		},
		methods: {
			delete_item: function(index, delete_id, delete_name) {
				// empty previous errors
				this.msg = [];
				if ( confirm(`Are you sure you wish to remove ${delete_name} from the database? `) ) {
					axios
					.delete(`${wpApiSettings.url}?brand_id=${delete_id}`)
					.then(response => {
						console.log(response);
						this.cm_toplist_data.splice(index, 1);

						// Output response msg
						this.msg.push(response.data)
					})
					.catch(e => {	
					console.log(e);
					// add the new errors
					this.msg.push(e);
					});
				}
			},
			add_new: function() {
				// empty previous errors
				this.msg = [];

				axios
				.post( wpApiSettings.url, {
					headers: { 'X-WP-Nonce': wpApiSettings.nonce },
					withCredentials: true,
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

					// Output response msg
					this.msg.push(response.data)
					
					// empty the input data
					this.casino_name = '';
					this.casino_rating = '';
				})
				.catch(e => {			
					console.log(e);
					// add the new errors
					this.msg.push(e);
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
        this.msg.push(e);
      });
    }
  });
})();