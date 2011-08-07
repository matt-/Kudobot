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
    @kudos = Kudo.select('count(kudos.id) as kudo_counts,users.*')
      .joins(:user)
      .where(:from_id => current_user.id)
      .group("users.id")
      .order("count(kudos.id) desc")
  end
  
  def received
    @kudos = Kudo.select('count(kudos.id) as kudo_counts,users.*')
      .joins(:from_user)
      .where(:user_id => current_user.id)
      .group("users.id")
      .order("count(kudos.id) desc")  
  end
  
end
