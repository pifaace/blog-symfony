namespace :composer do
  desc 'Install dependencies'
  task :install do
    on roles(:app) do
      within release_path do
        execute 'docker-compose' , :run, '--no-deps', '--rm', 'symfony-blog', :composer, :install, '--quiet'
      end
    end
  end
end
