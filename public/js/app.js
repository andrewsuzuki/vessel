window.Vessel = Ember.Application.create();

Vessel.ApplicationAdapter = DS.FixtureAdapter.extend();

Vessel.Router.map(function() {
	this.resource('pages', {path: 'pages'}, function() {
		this.route('new');
	});
});

Vessel.IndexRoute = Ember.Route.extend({
	redirect: function() {
		this.transitionTo('pages');
	}
});

Vessel.PagesRoute = Ember.Route.extend({
	model: function() {
		return this.store.find('page');
	}
});

Vessel.Page = DS.Model.extend({
	title: DS.attr('string'),
	isCompleted: DS.attr('boolean')
});

Vessel.Page.FIXTURES = [
{
	id: 1,
	title: 'Home Page',
	isCompleted: true
},
{
	id: 2,
	title: 'About PAgee',
	isCompleted: false
},
{
	id: 3,
	title: 'Blog PAge',
	isCompleted: true
}
];
