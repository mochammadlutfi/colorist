import { Ability } from '@casl/ability';

// Buat instance ability
export const ability = new Ability([]);

// Fungsi untuk update ability rules
export const updateAbility = (permissions) => {
    console.log('Updating ability with permissions:', permissions);
  
    const rules = permissions.map(permission => {
      const [subject, action] = permission.split('.');
      
      return {
        action,
        subject,
      };
    });
  
    console.log('New ability rules:', rules);
    ability.update(rules);
  };