task :send_daily_stats => :environment do
  
  @kudos = Kudo.find_by_sql("select k.from_id,u.first_name,u.last_name,
            u.avatar,
            u2.first_name as from_name,u2.last_name as from_last,u2.avatar as from_avatar,k.reason,DATE_FORMAT(k.created_at,'%M %D, %Y') as dDate,
            k.thread_id
             from kudos k
                            inner join users u on u.id = k.user_id
                            inner join users u2 on u2.id = k.from_id
  
                            where DATEDIFF(DATE_FORMAT(k.created_at,'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d')) = -1 and sent = 0 
  
                              order by k.rekudo,RAND() desc")
                              

  unless @kudos.blank?
    users = User.all
    users.each do |u|
      KudoMailer.daily_stats(@kudos,u).deliver
    end
  end
end