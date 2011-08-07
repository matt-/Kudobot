class KudoMailer < ActionMailer::Base
  default :from => "kudobot@jibjab.net"
  
  def kudo_email(kudo)
    @kudo = kudo
    mail(:to => @kudo.user.email,
         :subject => "KudoBot has sent you kudos")
  end
  
  def daily_stats(kudos,user)
    @kudos = kudos
     
      mail(:to => user.email,
         :subject => "KudoBot Daily Kudos")
  
  end
end
