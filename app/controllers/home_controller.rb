class HomeController < ApplicationController
  before_filter :authenticate_user!#, :except => "index"
  
  
  def index
    
    unless params[:rekudo].blank?
      @rekudo = Kudo.find_by_thread_id(params[:rekudo],:conditions => ["user_id <> ?",current_user.id])
      if @rekudo.blank?
        redirect_to "/"
        return
      else
        @rekudo_user_name = @rekudo.user.first_name + " " + @rekudo.user.last_name
        @rekudo_avatar = "<img src='/images/users/"  + @rekudo.user.avatar + "'/>"
        @rekudo_msg = "RK  @" + @rekudo.from_user.first_name + " to @" + @rekudo.from_user.last_name + ": For " + @rekudo.reason;
      end
    end 
     
    unless params[:kudo].blank?
      @kudo = Kudo.new(params[:kudo])
      @kudo.from_id = current_user.id
      if params[:rekudo].blank?
        @kudo.thread_id = rand(99999999) + Time.now.to_i
        @kudo.rekudo = 0 
      else
        @kudo.thread_id = params[:rekudo]
        @kudo.rekudo = 1
      end
      @kudo.save
      KudoMailer.kudo_email(@kudo).deliver
    end
  end
  
  def receive
    @kudo = Kudo.find(params[:id])
    unless @kudo.user.id.eql?(current_user.id)
      @kudo = nil
    end
    unless @kudo.blank?
      @kudo.update_attribute("received_at",Time.now)
    end
  end
  
  
  # autocomplete ajax result action
  def users
    @users = User.all(:conditions => ["(username like :user or first_name like :user
       or last_name like :user) and id <> :id",{:id => current_user.id,:user => params[:alias_parameter] + "%"}])
    render :partial => "users"
  end
  
  def given
    sql = "select count(k.id) as kcount,u.first_name,u.last_name,(select count(*) from kudos where from_id = " + current_user.id.to_s +  ") as ttl from kudos k
                                  inner join users u on u.id = k.user_id
                                  where k.from_id = " + current_user.id.to_s + " group by u.first_name,u.last_name order by count(k.id) desc"
    @kudos = Kudo.find_by_sql(sql)
    
    @hist = Kudo.find_by_sql("select k.id as kudo_id,DATE_FORMAT(k.created_at,'%m/%d/%Y') as created_at,
                              u1.first_name as sender_first_name,
                              u1.last_name as sender_last_name,
                              u2.first_name as recip_first_name,
                              u2.last_name as recip_last_name,
                              u1.avatar as sender_avatar ,
                              k.reason
               from kudos k
                                      inner join users u1 on u1.id = k.from_id
                                      inner join users u2 on u2.id = k.user_id

                                      where k.from_id = " + current_user.id.to_s + " order by k.id desc")
    
  end
  
  def received
  
    sql = "select count(k.id) as kcount,u.first_name,u.last_name,(select count(*) from kudos where user_id = " + current_user.id.to_s + ") as ttl from kudos k
                                  inner join users u on u.id = k.user_id
                                  where k.user_id = " + current_user.id.to_s + " group by u.first_name,u.last_name order by count(k.id) desc"
    @kudos = Kudo.find_by_sql(sql)
    
    @hist = Kudo.find_by_sql("select k.id as kudo_id,DATE_FORMAT(k.created_at,'%m/%d/%Y') as created_at,
                              u1.first_name as sender_first_name,
                              u1.last_name as sender_last_name,
                              u2.first_name as recip_first_name,
                              u2.last_name as recip_last_name,
                              u1.avatar as sender_avatar ,
                              k.reason
               from kudos k
                                      inner join users u1 on u1.id = k.from_id
                                      inner join users u2 on u2.id = k.user_id

                                      where k.user_id = " + current_user.id.to_s + " order by k.id desc")
  
  
  end
  
end
