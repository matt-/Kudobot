task :send_daily_stats => :environment do

  @kudos = Kudo.all(:select => "kudos.*,DATE_FORMAT(kudos.created_at,'%M %D, %Y') as dDate", 
  :conditions => "DATEDIFF(DATE_FORMAT(created_at,'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d')) = -1 and sent = 0", :order => "ifnull(kudos.rekudo,0)")


  unless @kudos.blank?
    users = User.all
    users.each do |u|
      KudoMailer.daily_stats(@kudos,u).deliver
    end
  end
  
  Kudo.update_all("sent = 1", "DATEDIFF(DATE_FORMAT(created_at,'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d')) = -1 and sent = 0")
end