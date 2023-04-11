export const translations = {
    methods: {
        translate(text) {
            let t = this.lang[text];
            
            if (t !== undefined) 
                return t;
            else
                return text
        }
    }
}