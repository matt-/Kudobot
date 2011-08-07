class Admin::KudosController < ApplicationController
   layout "admin"
   
   def show
     
    @kudos = Kudo.joins(:from_user,:user).order("id desc").page(params[:page]).per(50)
    
   end
   
   def destroy
     Kudo.delete(params[:id])
     redirect_to "/admin/kudos"
   end
   
   def stats
     @given = Kudo.select("count(kudos.id) as kudo_counts,users.id,users.username,MONTH(kudos.created_at) as mt,YEAR(kudos.created_at) as yr")
       .joins(:from_user)
       .group("users.id,users.username,MONTH(kudos.created_at),YEAR(kudos.created_at)")
       .order("count(kudos.id) desc")
     @recv = Kudo.select("count(kudos.id) as kudo_counts,users.id,users.username,MONTH(kudos.created_at) as mt,YEAR(kudos.created_at) as yr")
         .joins(:user)
         .group("users.id,users.username,MONTH(kudos.created_at),YEAR(kudos.created_at)")
         .order("count(kudos.id) desc")
   end
end
