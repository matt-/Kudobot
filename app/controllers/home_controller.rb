class HomeController < ApplicationController
  before_filter :authenticate_user!
  
  
  def index
    
    unless params[:rekudo].blank?
      @rekudo = Kudo.find_by_thread_id(params[:rekudo])
      if @rekudo.blank?
        redirect_to "/"
        return
      end
      @rekudo_msg = "RK  @" + @rekudo.from_user.first_name + " To " 
      @rekudo_users = @rekudo.users.where(["user_id <> ?",current_user.id])
      unless @rekudo_users.blank?
        @rekudo_users.each do |u|
          @rekudo_msg << "@" + u.name + " "
        end
      end
      @rekudo_msg << ": " + @rekudo.reason;
      
    end
    
    unless params[:selected_users].blank?
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
      
      params[:selected_users].each do |u|
        ku = UserKudo.new
        ku.user_id = u
        ku.kudo_id = @kudo.id
        ku.save
        KudoMailer.kudo_email(@kudo,u).deliver
      end
    end
  end
  
  def receive
   # @kudo = Kudo.find(params[:id])
    ku = UserKudo.find_all_by_kudo_id(params[:id],:conditions => ["user_id = ?",current_user.id])
    unless ku.blank?
      @kudo = ku[0].kudo
    end
    unless @kudo.blank?
      ku[0].update_attribute("received_date",Time.now)
    end
  end
  
  
  # autocomplete ajax result action
  def users
    
    param = params[:alias_parameter]
    if(param.include?(","))
      parray = param.split(",")
      param = parray[parray.length-1]
    end
    if param.length > 2 
    @users = User.all(:conditions => ["(username like :user or first_name like :user
       or last_name like :user) and id <> :id",{:id => current_user.id,:user => param + "%"}])
    end
    render :partial => "users"
    
  end
  
  def given    
 
    @given = UserKudo.select('count(distinct kudo_id) as kudo_counts,users.*')
      .joins(:kudo)
      .joins(:user)
      .where("kudos.from_id" => current_user.id)
      .group("users.id")
      .order("count(distinct kudo_id) desc")
    @kudos = Kudo.where("kudos.from_id" => current_user.id)
              .joins(:from_user)
              .joins(:user)
              .order("kudos.id desc").page(params[:page])
  end
  
  def received
    @received = UserKudo.select('count(user_kudos.id) as kudo_counts,users.*')
      .joins(:kudo)
      .joins("INNER JOIN users on users.id = kudos.from_id")
      .where("user_kudos.user_id" => current_user.id.to_s)
      .group("kudos.from_id")
      
      @kudos = Kudo
        .joins(:user_kudos)
        .joins("INNER JOIN users on users.id = kudos.from_id")
        .where("user_kudos.user_id" => current_user.id)
        .order("kudos.id desc").page(params[:page])

 
  end
  

  
end
