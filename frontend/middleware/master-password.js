export default function ({params, store, route, redirect}) {
	if (!store.state.masterPassword && route.name != 'password' && route.name != 'login') {
		redirect('301', '/password')
	}

}