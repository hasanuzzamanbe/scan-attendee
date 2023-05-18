// Daniel Shiffman
// http://youtube.com/thecodingtrain
// https://thecodingtrain.com/CodingChallenges/111-animated-sprite.html

// Horse Spritesheet from
// https://opengameart.org/content/2d-platformer-art-assets-from-horse-of-spring

// Animated Sprite
// https://youtu.be/3noMeuufLZY

class Sprite {
    constructor(animation, x, y, speed, width, height) {
        this.x = x;
        this.y = y;
        this.animation = animation;
        this.w = this.animation[0].width;
        this.len = this.animation.length;
        this.speed = speed;
        this.index = 0;
        this.width = width
        this.height = height??width

        this.testSpeed = 0.3;


    }

    show() {
        let index = floor(this.index) % this.len;
        image(this.animation[index], this.x, this.y, this.width, this.height);
    }

    updateCoordinate(x,y){
        this.x = x
        this.y = y
    }


    updateSpeed(speed){
        this.testSpeed = speed;
    }

    animate() {
        this.index += this.testSpeed;
        //this.x += this.speed * 15;

        if (this.x > width) {
            //this.x = -this.w;
        }

        //this.y++;
    }
}