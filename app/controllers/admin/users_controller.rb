class Admin::UsersController < ApplicationController
  layout "admin", :except => :change_pass
  before_filter :authenticate_user!,:is_admin
  def show
    @users = User.page(params[:page]).per(25)
  end
  
  def edit
    @user = User.find(params[:id])
  end
  
  def create
    @user = User.find(params[:id])
  end
  
  def update
    flash[:error] = nil
    flash[:notice] = nil
    @user = User.find(params[:id])
    unless params[:user][:avatar].blank?
      pic = params[:user][:avatar]
      name =  pic.original_filename
      directory = "public/images/users"
      path = File.join(directory, name)
      File.open(path, "wb") { |f| f.write(pic.read) }
      @user.resize_photo(path)
      params[:user][:avatar] = @user.id.to_s + ".png"
    end
    if @user.update_attributes(params[:user])
      flash[:notice] = "User updated"
    else
      flash[:error] = "ERROR: "
        @user.errors.each do |e,k|
          flash[:error] += e.to_s.humanize + " " + k + ","
        end
    end
    
    render :edit
  end
  
  def destroy
    User.delete(params[:id])
    redirect_to "/admin/users"
  end
  
  def new
    @user = User.new
  end
  
  def create
    flash[:error] = nil
    flash[:notice] = nil
    unless params[:user][:avatar].blank?
      pic = params[:user][:avatar]
      name =  pic.original_filename
      directory = "public/images/users"
      path = File.join(directory, name)
      File.open(path, "wb") { |f| f.write(pic.read) }
      params[:user][:avatar] = name
    end
    @user = User.new(params[:user])
    if @user.valid?
      @user.save
      @user.resize_photo(path)
      params[:user][:avatar] = @user.id.to_s + ".png"
      flash[:notice] = "User created."
    else
      
      flash[:error] = "ERROR: "
        @user.errors.each do |e,k|
          flash[:error] += e.to_s.humanize + " " + k + ","
        end
    end
    render :new
  end
  
  def change_pass
    flash[:error] = nil
    flash[:notice] = nil
    @user = User.find(params[:id])
    unless params[:user].blank?
      if(@user.update_attributes(params[:user])) 
        flash[:notice] = "Password changed"
      else
        flash[:error] = "ERROR: "
          @user.errors.each do |e,k|
            flash[:error] += e.to_s.humanize + " " + k + ","
          end
      end

    end
  end
  
end
