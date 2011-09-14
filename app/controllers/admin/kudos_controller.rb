class Admin::KudosController < ApplicationController
   layout "admin",:except => :mailer_test
   before_filter :authenticate_user!,:is_admin
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
         
    @recv =  UserKudo.select('count(kudos.id) as kudo_counts,users.id,users.username,MONTH(kudos.created_at) as mt,YEAR(kudos.created_at) as yr')
           .joins(:kudo)
           .joins(:user)
           .group("users.username,MONTH(kudos.created_at),YEAR(kudos.created_at)")
           .order("count(kudos.id) desc")
   end
   
   def mailer_test
     @kudos = Kudo.all(:select => "kudos.*,DATE_FORMAT(kudos.created_at,'%M %D, %Y') as dDate",
     :conditions => "DATEDIFF(DATE_FORMAT(kudos.created_at,'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d')) = -1", :order => "ifnull(kudos.rekudo,0)")
     render :template => "kudo_mailer/daily_stats"

   end
end
