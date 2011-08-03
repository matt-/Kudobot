task :migrate_users => :environment do
#	@users = User.find_all_by_email("mike@michaeljaffe.com")
	@users = User.all
	@users.each do |u|
		u.update_attribute(:password,u.encrypted_password)
	end
end
