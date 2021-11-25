<template>
<div>
    <navbar></navbar>
    <div class="jumbotron">
		<h1>{{ title }}</h1>
	</div>
    <div>
        <h3>We Manage {{this.cont}}  Wallets</h3>
        <h3>We Have {{this.money}}  euros</h3>
    </div>
    
</div>
</template>

<script>
import Navbar from './navbar.vue';
export default {
    
    data: function (){
            return{
                title: 'Welcome To Virtual Wallet',
                cont: '0',
                money: null
            }
    },
    methods:{
         getWallets: function(){
				axios.get('api/walletsIndex')
				
					.then(response=>{ 
						this.cont = response.data.data.length
						
					});
				
            },
            getMoney: function(){
				axios.get('api/money')
					.then(response=>{ 
                        this.money = response.data
                        console.log(response.data);
					});
				
			},
    },
    
    components:{
        'navbar': Navbar,
    },
    
    mounted() {
        
        this.getWallets();
        this.getMoney();
    },
    sockets: {
        money(value) {
            
            this.$toast.success("valor superior a 1000 euros!!!!" , {
                duration: 20000
            });
    }
}
}
</script>